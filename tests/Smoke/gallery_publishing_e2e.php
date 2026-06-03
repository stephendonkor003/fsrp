<?php

use App\Models\GalleryMedia;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Foundation\Testing\Concerns\InteractsWithExceptionHandling;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

$app = require __DIR__ . '/bootstrap.php';

class GalleryPublishingSmoke
{
    use MakesHttpRequests;
    use InteractsWithAuthentication;
    use InteractsWithSession;
    use InteractsWithExceptionHandling;

    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function run(): void
    {
        Storage::fake('public');

        Artisan::call('db:seed', ['--class' => PermissionSeeder::class]);

        DB::beginTransaction();

        try {
            $user = $this->galleryUser();
            $title = 'E2E Gallery Media ' . Str::upper(Str::random(5));

            $this->actingAs($user)
                ->get(route('system.gallery.create'))
                ->assertOk()
                ->assertSee('Upload Gallery Media');

            $this->actingAs($user)
                ->postWithCsrf(route('system.gallery.store'), [
                    'title' => $title,
                    'category' => 'field_visits',
                    'description' => 'Gallery smoke test description.',
                    'caption' => 'Gallery smoke test caption.',
                    'alt_text' => 'Gallery smoke test image',
                    'captured_at' => now()->toDateString(),
                    'is_featured' => '1',
                    'media_file' => UploadedFile::fake()->image('field-visit.jpg', 900, 500),
                    'action' => 'submit',
                ])
                ->assertRedirect();

            $media = GalleryMedia::where('title', $title)->first();
            $this->assertTrue((bool) $media, 'Gallery media was not created.');
            $this->assertSame('submitted', $media->status, 'Gallery media was not submitted.');
            $this->assertSame('image', $media->media_type, 'Gallery media type was not detected as image.');

            $this->actingAs($user)
                ->get(route('system.gallery.edit', $media))
                ->assertOk()
                ->assertSee($title)
                ->assertSee('Save Approval');

            $this->get(route('gallery.index'))->assertOk()->assertDontSee($title);

            $this->actingAs($user)
                ->postWithCsrf(route('system.gallery.approve', $media), [
                    'status' => 'published',
                    'review_notes' => 'Approved for publication.',
                ])
                ->assertRedirect();

            $media->refresh();
            $this->assertSame('published', $media->status, 'Gallery media was not published.');
            $this->assertTrue($media->approved_at !== null, 'Published gallery media was not approved.');

            $this->get(route('gallery.index'))
                ->assertOk()
                ->assertSee($title)
                ->assertSee('Gallery smoke test caption');

            echo "GALLERY_E2E_OK\n";
        } finally {
            DB::rollBack();
        }
    }

    private function galleryUser(): User
    {
        $role = Role::firstOrCreate(['name' => 'Communications Officer'], ['description' => 'Communications officer']);
        $permissionIds = Permission::whereIn('name', ['gallery.manage', 'gallery.approve'])->pluck('id')->all();
        $role->permissions()->syncWithoutDetaching($permissionIds);

        return User::create([
            'name' => 'E2E Gallery Officer',
            'email' => 'e2e-gallery-' . Str::lower(Str::random(6)) . '@example.test',
            'password' => Hash::make('Password123!'),
            'user_type' => 'admin',
            'role_id' => $role->id,
            'must_change_password' => false,
        ]);
    }

    private function postWithCsrf(string $uri, array $data = [])
    {
        $token = Str::random(40);

        return $this->withSession(['_token' => $token])
            ->post($uri, ['_token' => $token, ...$data]);
    }

    private function assertTrue(bool $condition, string $message): void
    {
        if (! $condition) {
            throw new RuntimeException($message);
        }
    }

    private function assertSame($expected, $actual, string $message): void
    {
        if ($expected !== $actual) {
            throw new RuntimeException($message);
        }
    }
}

(new GalleryPublishingSmoke($app))->run();
