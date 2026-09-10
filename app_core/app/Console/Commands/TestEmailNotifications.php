<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Listing;
use App\Models\ChatThread;
use App\Models\ChatMessage;
use App\Models\Offer;

class TestEmailNotifications extends Command
{
    protected $signature   = 'test:emails {--to=fuzooly@gmail.com} {--type=all}';
    protected $description = 'Send a real test email for every notification type to verify SMTP delivery';

    private string $to;
    private array  $results = [];

    public function handle(): int
    {
        $this->to = $this->option('to');
        $type     = $this->option('type');

        $this->info("Sending test emails to: {$this->to}");
        $this->info(str_repeat('─', 60));

        $tests = [
            'verify_account'    => fn () => $this->testVerifyAccount(),
            'password_reset'    => fn () => $this->testPasswordReset(),
            'listing_status'    => fn () => $this->testListingStatus(),
            'chat_message'      => fn () => $this->testChatMessage(),
            'contact_message'   => fn () => $this->testContactMessage(),
            'offer_accepted'    => fn () => $this->testOfferAccepted(),
            'price_drop'        => fn () => $this->testPriceDrop(),
            'saved_search'      => fn () => $this->testSavedSearchAlert(),
            'listing_renewal'   => fn () => $this->testListingRenewal(),
        ];

        $run = $type === 'all' ? $tests : array_intersect_key($tests, [$type => true]);

        foreach ($run as $name => $fn) {
            $this->runTest($name, $fn);
        }

        $this->printSummary();
        $pass = array_sum(array_column($this->results, 'pass'));
        return $pass === count($this->results) ? 0 : 1;
    }

    private function runTest(string $name, callable $fn): void
    {
        try {
            $fn();
            $this->results[$name] = ['pass' => true, 'error' => null];
            $this->line("  <fg=green>✓</> {$name}");
        } catch (\Throwable $e) {
            $this->results[$name] = ['pass' => false, 'error' => $e->getMessage()];
            $this->line("  <fg=red>✗</> {$name}: " . substr($e->getMessage(), 0, 120));
            \Log::error("test:emails [{$name}] failed", ['error' => $e->getMessage()]);
        }
    }

    // ── Mail tests ──────────────────────────────────────────────────────────

    private function testVerifyAccount(): void
    {
        $user = $this->fakeUser();
        $user->verification_token = 'test-token-' . Str::random(16);
        Mail::to($this->to)->send(new \App\Mail\VerifyAccountMail($user));
    }

    private function testPasswordReset(): void
    {
        $resetUrl = url('/reset-password/test-token-abc123?email=' . urlencode($this->to));
        Mail::to($this->to)->send(new \App\Mail\PasswordResetMail($resetUrl));
    }

    private function testListingStatus(): void
    {
        $listing = $this->fakeListing();
        Mail::to($this->to)->send(new \App\Mail\ListingStatusMail($listing, true));
    }

    private function testChatMessage(): void
    {
        $listing = $this->fakeListing();

        $thread = new ChatThread();
        $thread->id = 1;
        // Attach the listing as a loaded relation so the view can read it
        $thread->setRelation('listing', $listing);

        $msg = new ChatMessage();
        $msg->message = 'Hi, is this still available? I am very interested in buying it today.';

        Mail::to($this->to)->send(new \App\Mail\NewChatMessageMail($thread, $msg, 'Test Buyer'));
    }

    private function testContactMessage(): void
    {
        Mail::to($this->to)->send(new \App\Mail\ContactMessageMail(
            'Test User',
            $this->to,
            "Hello,\n\nThis is a test contact form message sent via the automated email tester.\n\nRegards,\nTest User"
        ));
    }

    private function testOfferAccepted(): void
    {
        $listing = $this->fakeListing();
        $buyer   = $this->fakeUser('Test Buyer');

        $offer = new Offer();
        $offer->id             = 1;
        $offer->offered_price  = 18500;
        $offer->payment_method = 'cash';
        $offer->payment_label  = 'Cash on Delivery';
        $offer->seller_note    = 'Please meet at Kegalle Town Center at 10am.';
        $offer->setRelation('listing', $listing);
        $offer->setRelation('buyer', $buyer);

        Mail::to($this->to)->send(new \App\Mail\OfferAcceptedMail($offer));
    }

    private function testPriceDrop(): void
    {
        $drops = collect([
            (object)[
                'title'     => 'Samsung Galaxy A55 – Like New',
                'category'  => 'Phones & Tablets',
                'old_price' => 75000,
                'new_price' => 62000,
                'slug'      => 'samsung-galaxy-a55-like-new',
            ],
            (object)[
                'title'     => 'Honda Wave 110 – 2021',
                'category'  => 'Bikes & Scooters',
                'old_price' => 210000,
                'new_price' => 185000,
                'slug'      => 'honda-wave-110-2021',
            ],
        ]);

        Mail::to($this->to)->send(new \App\Mail\PriceDropMail($drops));
    }

    private function testSavedSearchAlert(): void
    {
        $listings = collect([
            $this->fakeListing('iPhone 14 – 128GB Space Black'),
            $this->fakeListing('iPhone 14 Pro – Damaged Screen'),
        ]);

        Mail::to($this->to)->send(new \App\Mail\SavedSearchAlertMail(
            $listings,
            ['q' => 'iPhone', 'location' => 'Kegalle']
        ));
    }

    private function testListingRenewal(): void
    {
        Mail::to($this->to)->send(new \App\Mail\ListingRenewalMail($this->fakeListing()));
    }

    // ── Fake model helpers ──────────────────────────────────────────────────

    private function fakeUser(string $name = 'Test User'): User
    {
        $user        = new User();
        $user->id    = 9999;
        $user->name  = $name;
        $user->email = $this->to;
        $user->phone = '0712345678';
        return $user;
    }

    private function fakeListing(string $title = 'Test Listing – Samsung TV 55"'): Listing
    {
        $listing           = new Listing();
        $listing->id       = 9999;
        $listing->title    = $title;
        $listing->slug     = Str::slug($title);
        $listing->price    = 22500;
        $listing->location = 'Kegalle';
        $listing->status   = 'approved';
        return $listing;
    }

    private function printSummary(): void
    {
        $pass  = array_sum(array_column($this->results, 'pass'));
        $total = count($this->results);
        $this->info(str_repeat('─', 60));
        $this->info("Result: {$pass}/{$total} emails sent successfully");
        if ($pass === $total) {
            $this->info("All done. Check {$this->to} inbox in 1–2 minutes.");
        } else {
            $this->warn('Some emails failed — run `php artisan queue:work` if using queues, and check storage/logs/laravel.log.');
        }

        \Log::info('test:emails run completed', [
            'to'      => $this->to,
            'pass'    => $pass,
            'total'   => $total,
            'results' => $this->results,
        ]);
    }
}
