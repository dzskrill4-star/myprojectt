<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportMessage extends Model
{
    protected $fillable = ['support_ticket_id', 'admin_id', 'message', 'is_read'];

    public function ticket(){
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id', 'id');
    }

    public function admin(){
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function attachments()
    {
        return $this->hasMany(SupportAttachment::class,'support_message_id','id');
    }

    /**
     * Scope to get unread messages for a specific user
     */
    public function scopeUnreadByUser($query, $userId)
    {
        return $query->where('is_read', 0)
                    ->whereHas('ticket', function($q) use ($userId) {
                        $q->where('user_id', $userId);
                    })
                    ->whereNotNull('admin_id');
    }
}
