<?php

namespace App\Jobs;

use App\Helpers\ImpactExportHelper;
use App\Helpers\ImpactHelper;
use App\Mail\ExportFailed;
use App\Models\Impact;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class GenerateExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var $user */
    protected $user;

    /** @var $email */
    protected $email;

    /** @var $type */
    protected $type;

    /** @var $ids */
    protected $ids;

    /**
     * @var integer
     */
    public $timeout;

    /**
     * GenerateExport constructor.
     *
     * @param array $filter
     * @param User $user
     * @param string $email
     * @param string $type
     * @param array $ids
     */
    public function __construct($filter, $user, $email, $type, $ids)
    {
        $this->user = $user;
        $this->email = $email;
        $this->type = $type;
        $this->ids = $ids;

        if (is_null($this->ids) || empty($this->ids)) {
            $searchFields = ImpactHelper::defaultSearchFields();
            $searchFields = array_merge($searchFields, $filter);
            $searchRules = ImpactHelper::buildFilterQuery($searchFields);
            $query = Impact::search('ANY')
                ->rule(function () use ($searchRules) {
                    return $searchRules;
                })
                ->select(['id', 'category_id'])
                ->where('active', true);
            $this->ids = $query->take(500000)->keys()->all();
        }

        $this->timeout = isset($this->ids) ? (ceil(count($this->ids) / 1000) * 60) : 600;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     */
    public function handle()
    {
        $filePath = ImpactExportHelper::exportImpacts($this->user, $this->type, $this->ids);
        ImpactExportHelper::sendEmail($this->user, $this->email, $filePath);
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        $firstName = $this->user->firstName;
        $lastName = $this->user->lastName;
        Mail::to($this->email)->queue(new ExportFailed("$firstName $lastName"));
    }
}
