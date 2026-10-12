<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property string $id
 * @property string $street
 * @property string $house_number
 * @property string|null $local_number
 * @property string $city
 * @property string $postal_code
 * @property string $country
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Guest> $guests
 * @property-read int|null $guests_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereHouseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereLocalNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereStreet($value)
 */
	class Address extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $wedding_id
 * @property string $created_by
 * @property string $name
 * @property string $color
 * @property bool $wedding_day_calendar
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CalendarEvent> $events
 * @property-read int|null $events_count
 * @property-read \App\Models\Wedding|null $wedding
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereWeddingDayCalendar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereWeddingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar withoutTrashed()
 */
	class Calendar extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $calendar_id
 * @property string $created_by
 * @property \Illuminate\Support\Carbon $date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Calendar|null $calendar
 * @property-read \App\Models\User $creator
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarEvent onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarEvent whereCalendarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarEvent whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarEvent whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarEvent whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarEvent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarEvent withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarEvent withoutTrashed()
 */
	class CalendarEvent extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $wedding_id
 * @property string $created_by
 * @property string $name
 * @property float $amount
 * @property string|null $category
 * @property string|null $contact
 * @property \Illuminate\Support\Carbon|null $payment_deadline
 * @property string|null $payment_status
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $creator
 * @property-read \App\Models\Wedding|null $wedding
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance wherePaymentDeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereWeddingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance withoutTrashed()
 */
	class Finance extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $wedding_id
 * @property string|null $table_id
 * @property string $created_by
 * @property string|null $address_id
 * @property string|null $main_guest_id
 * @property string $name
 * @property string $surname
 * @property \App\Enums\AcquaintanceTypesEnum $acquaintance_type
 * @property bool $child
 * @property bool $groom_guest
 * @property bool $invitation_prepared
 * @property bool $invitation_delivered
 * @property bool $confirmed_attendance
 * @property \App\Enums\DietsEnum $diet
 * @property float|null $table_position_x
 * @property float|null $table_position_y
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Address|null $address
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Guest> $companions
 * @property-read int|null $companions_count
 * @property-read \App\Models\User $creator
 * @property-read Guest|null $mainGuest
 * @property-read \App\Models\Table|null $table
 * @property-read \App\Models\Wedding|null $wedding
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereAcquaintanceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereChild($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereConfirmedAttendance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereDiet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereGroomGuest($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereInvitationDelivered($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereInvitationPrepared($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereMainGuestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereTableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereTablePositionX($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereTablePositionY($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereWeddingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest withoutTrashed()
 */
	class Guest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $wedding_id
 * @property string $created_by
 * @property string $name
 * @property int|null $capacity
 * @property float|null $position_x
 * @property float|null $position_y
 * @property float|null $width
 * @property float|null $height
 * @property float|null $radius
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Guest> $guests
 * @property-read int|null $guests_count
 * @property-read \App\Models\Wedding|null $wedding
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table wherePositionX($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table wherePositionY($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table whereRadius($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table whereWeddingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table whereWidth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table withoutTrashed()
 */
	class Table extends \Eloquent {}
}

namespace App\Models{
/**
 * @method static static create(array<string, mixed> $attributes = [])
 * @method static Builder|User where(string|array|\Closure $column, mixed $operator = null, mixed $value = null)
 * @property string $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CalendarEvent> $createdCalendarEvents
 * @property-read int|null $created_calendar_events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Calendar> $createdCalendars
 * @property-read int|null $created_calendars_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Finance> $createdFinances
 * @property-read int|null $created_finances_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Guest> $createdGuests
 * @property-read int|null $created_guests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Table> $createdTables
 * @property-read int|null $created_tables_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Wedding> $createdWeddings
 * @property-read int|null $created_weddings_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WeddingUser> $weddingUsers
 * @property-read int|null $wedding_users_count
 * @property-read \App\Models\WeddingUser|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Wedding> $weddings
 * @property-read int|null $weddings_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $created_by
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $invite_code
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Calendar> $calendars
 * @property-read int|null $calendars_count
 * @property-read \App\Models\User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Finance> $finances
 * @property-read int|null $finances_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Table> $tables
 * @property-read int|null $tables_count
 * @property-read \App\Models\WeddingUser|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WeddingUser> $weddingUsers
 * @property-read int|null $wedding_users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wedding newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wedding newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wedding onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wedding query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wedding whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wedding whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wedding whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wedding whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wedding whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wedding whereInviteCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wedding whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wedding whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wedding withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wedding withoutTrashed()
 */
	class Wedding extends \Eloquent {}
}

namespace App\Models{
/**
 * @property UserRolesEnum $role
 * @property-read string $role_label
 * @method static static create(array<string, mixed> $attributes = [])
 * @method static Builder|WeddingUser where(string|array|\Closure $column, mixed $operator = null, mixed $value = null)
 * @property string $id
 * @property string $user_id
 * @property string $wedding_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $is_active
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Wedding|null $wedding
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeddingUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeddingUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeddingUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeddingUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeddingUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeddingUser whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeddingUser whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeddingUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeddingUser whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeddingUser whereWeddingId($value)
 */
	class WeddingUser extends \Eloquent {}
}

