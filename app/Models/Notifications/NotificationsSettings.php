<?php

namespace App\Models\Notifications;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationsSettings extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'notification_settings';

}
