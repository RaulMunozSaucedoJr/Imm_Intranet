<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /* The line `protected  = 'admin_users';` in the User model is specifying the database table
    that the model should be associated with. In this case, it indicates that the User model should
    be linked to the 'admin_users' table in the database. By setting this property, Laravel will
    automatically assume that the User model corresponds to the 'admin_users' table when performing
    database operations such as querying or saving data. */
    protected $table = 'admin_users';

    /* The `protected ` property in the User model is used to specify which attributes are mass
    assignable. This means that the attributes listed in the `` array can be mass assigned
    using methods like `create` or `update` on the model. */
    protected $fillable = [
        'employeeNumber',
        'username',
        'name',
        'entryDate',
        'periodOne',
        'periodTwo',
        'periodThree',
        'originalPeriodOne',
        'originalPeriodTwo',
        'originalPeriodThree',
        'area',
        'boss',
        'totalDays',
        'headquarter',
        'password',
        'email',
        'name',
    ];

    /* The `protected ` property in the User model is used to specify which attributes should not
    be included in the serialized representation of the model when it is converted to an array or JSON.
    In this case, the 'password' and 'remember_token' attributes are marked as hidden, meaning that
    when the User model is serialized, these attributes will not be visible in the output. This is
    commonly used for sensitive information that should not be exposed when the model is converted to a
    readable format for security and privacy reasons. */
    protected $hidden = [
        'password',
        'remember_token',
    ];

/* The `protected ` property in the User model is used to specify how certain attributes should
be cast to native types. In this case: */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The function subtracts vacation days from a user's available periods until the specified number
     * of days is reached.
     *
     * @param userId The `userId` parameter in the `subtractVacationDays` function is used to identify
     * the user for whom vacation days need to be subtracted. It is the unique identifier for the user
     * in the system.
     * @param days The `subtractVacationDays` function is used to subtract vacation days from a user's
     * vacation periods. The function takes two parameters:
     */
    public static function subtractVacationDays($userId, $days)
    {
        $user = self::find($userId);
        if ($user) {
            while ($days > 0) {
                if (!empty($user->periodOne)) {
                    $days = self::subtractDaysFromField($user, 'periodOne', $days, 'periodOne');
                } elseif (!empty($user->periodTwo)) {
                    $days = self::subtractDaysFromField($user, 'periodTwo', $days, 'periodTwo');
                } elseif (!empty($user->periodThree)) {
                    $days = self::subtractDaysFromField($user, 'periodThree', $days, 'periodThree');
                } else {
                    // No hay más períodos para restar
                    break;
                }
            }
            $user->save();
        }
    }

    private static function subtractDaysFromField($user, $field, $days, $period)
    {
        $remainingDays = $user->$field;
        $remainingDays -= $days;

        if ($remainingDays < 0) {
            $days -= $user->$field;
            $user->$field = 0;
        } else {
            $user->$field = $remainingDays;
            $days = 0;
        }

        // Marcamos el periodo que se está modificando
        $user->lastModifiedPeriod = $period;

        return $days;
    }

    public function addVacationDaysDynamically($daysDifference)
    {
        if ($daysDifference >= 0) {
            if ($this->periodOne <= 0) {
                $this->periodOne += $daysDifference;
                $this->lastModifiedPeriod = 'periodOne'; // Marcar el período modificado
            } elseif ($this->periodTwo <= 0) {
                $this->periodTwo += $daysDifference;
                $this->lastModifiedPeriod = 'periodTwo'; // Marcar el período modificado
            } elseif ($this->periodThree <= 0) {
                $this->periodThree += $daysDifference;
                $this->lastModifiedPeriod = 'periodThree'; // Marcar el período modificado
            } else {
                // Si todos los períodos están ocupados, verifica si alguno ha tenido días extraídos
                if ($this->periodOne < $this->originalPeriodOne) {
                    $this->periodOne += $daysDifference;
                    $this->lastModifiedPeriod = 'periodOne';
                } elseif ($this->periodTwo < $this->originalPeriodTwo) {
                    $this->periodTwo += $daysDifference;
                    $this->lastModifiedPeriod = 'periodTwo';
                } elseif ($this->periodThree < $this->originalPeriodThree) {
                    $this->periodThree += $daysDifference;
                    $this->lastModifiedPeriod = 'periodThree';
                } else {
                    // Aquí puedes manejar la lógica en caso de que no haya períodos disponibles
                }
            }

            $this->save();
        }
    }

}
