<?php

namespace App\Models;

use App\Models\Services\ServiceBooking;
use App\Models\Services\ServiceReview;
use App\Models\Shop\ProductOrder;
use App\Models\Shop\ProductReview;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
  use HasFactory, Notifiable;

  /**
   * The attributes that aren't mass assignable.
   *
   * @var array
   */
  protected $guarded = [];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  public function productOrder()
  {
    return $this->hasMany(ProductOrder::class, 'user_id', 'id');
  }

  public function productReview()
  {
    return $this->hasMany(ProductReview::class, 'user_id', 'id');
  }

  public function serviceBooking()
  {
    return $this->hasMany(ServiceBooking::class,'user_id','id');
  }
  public function serviceReview()
  {
    return $this->hasMany(ServiceReview::class, 'user_id', 'id');
  }

}
