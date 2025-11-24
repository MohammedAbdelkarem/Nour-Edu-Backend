<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Banner;
use App\Traits\StorageHelper;
use App\Enums\MediaStatusEnum;
use App\Models\Story;
use App\Models\Users\Reel\Reel;
use Illuminate\Console\Command;

class BannerRemoverCommand extends Command
{
    use StorageHelper;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'banner:remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to delete expired banners with there media';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bannerHour = banner_live_time();

        $banners = Banner::where('status',MediaStatusEnum::INACTIVE)
                ->orWhere('created_at', '<=', Carbon::now()->subHours($bannerHour))
                ->get();
        
        $banners->delete();

        $storyHour = reel_live_time();

        $stories = Story::where('end_at' , '<' , now())
                ->orWhere('status',MediaStatusEnum::INACTIVE)
                ->orWhere('created_at', '<=', Carbon::now()->subHours($storyHour))
                ->get();
        
        $stories->delete();


    }
}
