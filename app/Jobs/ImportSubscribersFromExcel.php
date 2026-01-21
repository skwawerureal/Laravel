<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\EmailList;
use App\Models\Subscriber;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportSubscribersFromExcel implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = [30, 60, 120];
    public $timeout = 300;

    protected $emailList;
    protected $filePath;

    public function __construct(EmailList $emailList, string $filePath)
    {
        $this->emailList = $emailList;
        $this->filePath = $filePath;
    }

    public function handle(): void
    {
        try {
            $fullPath = Storage::disk('local')->path($this->filePath);
            $spreadsheet = IOFactory::load($fullPath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $headers = array_shift($rows);
            $importedCount = 0;
            $skippedCount = 0;

            foreach ($rows as $row) {
                if (empty(array_filter($row))) {
                    continue;
                }

                $data = array_combine($headers, $row);
                $email = $data['email'] ?? $data['Email'] ?? $data['EMAIL'] ?? null;

                if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $skippedCount++;
                    continue;
                }

                $subscriber = Subscriber::firstOrCreate(
                    ['email' => $email],
                    [
                        'first_name' => $data['first_name'] ?? $data['First Name'] ?? $data['FIRST_NAME'] ?? null,
                        'last_name' => $data['last_name'] ?? $data['Last Name'] ?? $data['LAST_NAME'] ?? null,
                        'custom_fields' => array_diff_key($data, array_flip(['email', 'Email', 'EMAIL', 'first_name', 'First Name', 'FIRST_NAME', 'last_name', 'Last Name', 'LAST_NAME']))
                    ]
                );

                if (!$this->emailList->subscribers()->where('subscriber_id', $subscriber->id)->exists()) {
                    $this->emailList->subscribers()->attach($subscriber->id);
                    $importedCount++;
                } else {
                    $skippedCount++;
                }
            }

            $this->emailList->updateSubscriberCount();

            Storage::delete($this->filePath);

            Log::info("Import completed for email list {$this->emailList->id}: {$importedCount} imported, {$skippedCount} skipped");

        } catch (\Exception $e) {
            Log::error("Failed to import Excel file: " . $e->getMessage());
            Storage::delete($this->filePath);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Excel import job failed: " . $exception->getMessage());
        Storage::delete($this->filePath);
    }
}
