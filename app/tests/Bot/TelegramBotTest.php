<?php

namespace Tests\Unit\Services\Bot;

use App\Models\BotUser;
use App\Models\BotUserNotification;
use App\Models\User;
use App\Services\Bot\TelegramBot;
use App\Services\Normalizers\PhoneNormalizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Orchid\Support\Color;
use Tests\TestCase;

class TelegramBotTest extends TestCase
{
    use RefreshDatabase;

    protected BotUser $botUser;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'phone' => '79261234567'
        ]);

        $this->botUser = BotUser::factory()->create([
            'phone' => PhoneNormalizer::normalizePhone('79261234567'),
            'telegram_id' => '123456789'
        ]);
    }

    /** @test */
    public function it_can_get_user_by_phone()
    {
        $result = TelegramBot::getUser('79261234567');
        $this->assertEquals($this->botUser->id, $result->id);

        $result = TelegramBot::getUser('+7 (926) 123-45-67');
        $this->assertEquals($this->botUser->id, $result->id);

        $result = TelegramBot::getUser('79161234567');
        $this->assertNull($result);
    }

    /** @test */
    public function it_can_get_user_by_telegram_id()
    {
        $result = TelegramBot::getUserByUserId('123456789');
        $this->assertEquals($this->botUser->id, $result->id);

        $result = TelegramBot::getUserByUserId('999999999');
        $this->assertNull($result);
    }

    /** @test */
    public function it_can_store_new_bot_user()
    {
        $phone = '79161234567';
        $telegramId = '987654321';

        $result = TelegramBot::store($phone, $telegramId);

        $this->assertDatabaseHas('bot_users', [
            'phone' => PhoneNormalizer::normalizePhone($phone),
            'telegram_id' => $telegramId
        ]);

        $this->assertEquals(PhoneNormalizer::normalizePhone($phone), $result->phone);
    }

    /** @test */
    public function it_can_update_existing_bot_user()
    {
        $newTelegramId = '999999999';
        $result = TelegramBot::store('79261234567', $newTelegramId);

        $this->assertDatabaseHas('bot_users', [
            'phone' => PhoneNormalizer::normalizePhone('79261234567'),
            'telegram_id' => $newTelegramId
        ]);

        $this->assertEquals($this->botUser->id, $result->id);
    }

    /** @test */
    public function it_can_notify_user()
    {
        $notification = TelegramBot::notify(
            $this->user,
            'Test Title',
            'Test Body',
            Color::INFO,
            true
        );

        $this->assertDatabaseHas('bot_user_notifications', [
            'bot_user_id' => $this->botUser->id
        ]);

        $this->assertInstanceOf(BotUserNotification::class, $notification);
    }

    /** @test */
    public function it_throws_exception_when_notifying_non_existent_user()
    {
        $user = User::factory()->create([
            'phone' => '79161234567'
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('BotUser with phone "79161234567" undefined');

        TelegramBot::notify($user, 'Test', 'Test');
    }

    /** @test */
    public function it_can_notify_bot_user_directly()
    {
        $notification = TelegramBot::notifyBot(
            $this->botUser,
            'Direct Title',
            'Direct Body'
        );

        $this->assertDatabaseHas('bot_user_notifications', [
            'bot_user_id' => $this->botUser->id,
        ]);

        $this->assertStringContainsString('Direct Title', $notification->data);
    }

    /** @test */
    public function it_can_pop_all_notifications()
    {
        BotUserNotification::factory()->count(3)->create();

        $notifications = TelegramBot::pop();

        $this->assertCount(3, $notifications);
        $this->assertInstanceOf(Collection::class, $notifications);
        $this->assertEquals(0, BotUserNotification::count());
    }

    /** @test */
    public function it_can_pop_notifications_by_phone()
    {
        $phone = '79261234567';
        BotUserNotification::factory()->create(['bot_user_id' => $this->botUser->id]);
        BotUserNotification::factory()->create(['bot_user_id' => $this->botUser->id]);

        BotUserNotification::factory()->create();

        $notifications = TelegramBot::popByPhone($phone);

        $this->assertCount(2, $notifications);
        $this->assertEquals(1, BotUserNotification::count());
    }

    /** @test */
    public function it_can_pop_notifications_by_telegram_id()
    {
        $telegramId = '123456789';
        BotUserNotification::factory()->create(['bot_user_id' => $this->botUser->id]);
        BotUserNotification::factory()->create(['bot_user_id' => $this->botUser->id]);

        BotUserNotification::factory()->create();

        $notifications = TelegramBot::popByTelegramId($telegramId);

        $this->assertCount(2, $notifications);
        $this->assertEquals(1, BotUserNotification::count());
    }
}
