<?php namespace Twitter;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Psy\Exception\ErrorException;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

	public function follows($id)
	{
		foreach ($this->following as $following)
		{
			if ($following->id == $id)
			{
				return true;
			}
		}

		return false;
	}

	public function muted($id)
	{
		foreach ($this->mutes as $mutes)
		{
			if ($mutes->muted_id == $id)
			{
				return true;
			}
		}

		return false;
	}

	public function favorited($id)
	{
		foreach ($this->favorites as $favorite)
		{
			if ($favorite->post_id == $id)
			{
				return true;
			}
		}

		return false;
	}

	public function posted($id)
	{
		foreach ($this->posts as $post)
		{
			if ($post->id == $id)
			{
				return true;
			}
		}

		return false;
	}

	public function reposted($id)
	{
		foreach ($this->reposts as $repost)
		{
			if ($repost->post_id == $id)
			{
				return true;
			}
		}

		return false;
	}

	public function settings()
	{
		return $this->hasOne('Twitter\Settings');
	}

	public function posts()
	{
		return $this->hasMany('Twitter\Post')->orderBy('created_at', 'DESC');
	}

	public function reposts()
	{
		return $this->hasMany('Twitter\RePost');
	}

	public function favorites()
	{
		return $this->hasMany('Twitter\Favorite');
	}

	public function alerts()
	{
		return $this->hasMany('Twitter\Alert');
	}

	public function mutes()
	{
		return $this->hasMany('Twitter\Mute', 'user_id', 'id');
	}

	public function followers()
	{
		return $this->belongsToMany('Twitter\User', 'user_relations', 'followed_id', 'follower_id');
	}

	public function following()
	{
		return $this->belongsToMany('Twitter\User', 'user_relations', 'follower_id', 'followed_id');
	}

    public function profile()
    {
        return $this->hasOne('Twitter\Profile');
    }

    public function getDisplayNameAttribute()
    {
        $available = false;
        if (isset($this->profile->display_name)
            && !empty($this->profile->display_name))
        {
            $available = true;
        }

        return $available ?
            $this->profile->display_name :
            $this->name;
    }

    public function profileImage($size = 'small')
    {
        $webpath = 'images/no-thumb.png';

        try
        {
            $contents = null;
            switch ($size)
            {
                case 'small':
                    $contents = explode('/', $this->profile->image->small);
                    break;
                case 'tiny':
                    $contents = explode('/', $this->profile->image->tiny);
                    break;
                case 'medium':
                    $contents = explode('/', $this->profile->image->medium);
                    break;
                case 'large';
                    $contents = explode('/', $this->profile->image->large);
                    break;
                case 'actual':
                    $contents = explode('/', $this->profile->image->actual);
                    break;
            }
            $filename = array_pop($contents);
            $directory = array_pop($contents);
            $webpath = implode('/', ['images', $directory, $filename]);
        }
        catch (\ErrorException $e) {}

        return $webpath;
    }

}
