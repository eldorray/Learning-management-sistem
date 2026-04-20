<?php

namespace App\Livewire;

use App\Models\Notification;
use Livewire\Component;

class NotificationDropdown extends Component
{
    public bool $open = false;

    public function getNotificationsProperty()
    {
        return Notification::where('user_id', auth()->id())
            ->latest()
            ->limit(15)
            ->get();
    }

    public function getUnreadCountProperty(): int
    {
        return Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->count();
    }

    public function markRead(int $id): void
    {
        $notif = Notification::where('user_id', auth()->id())->findOrFail($id);
        $notif->markAsRead();
    }

    public function markAllRead(): void
    {
        Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function toggle(): void
    {
        $this->open = !$this->open;
        if ($this->open) {
            // Auto-mark as read when panel opens
            Notification::where('user_id', auth()->id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }
    }

    public function render()
    {
        return view('livewire.notification-dropdown');
    }
}
