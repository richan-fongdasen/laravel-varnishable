<?php

namespace RichanFongdasen\Varnishable\Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use RichanFongdasen\Varnishable\Events\ModelHasUpdated;
use RichanFongdasen\Varnishable\Tests\Supports\Models\Post;
use RichanFongdasen\Varnishable\Tests\Supports\Models\User;

class VarnishableObserverTests extends TestCase
{
    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        User::factory(3)->create();
    }

    #[Test]
    public function it_fires_model_has_updated_event_on_creating_new_record()
    {
        Event::fake([ModelHasUpdated::class]);

        User::factory()->create();

        Event::assertDispatched(ModelHasUpdated::class);
    }

    #[Test]
    public function it_fires_model_has_updated_event_on_updating_record()
    {
        Event::fake([ModelHasUpdated::class]);

        $user = User::first();
        $user->name = 'Taylor Otwell';
        $user->save();

        Event::assertDispatched(ModelHasUpdated::class);
    }

    #[Test]
    public function it_fires_model_has_updated_event_on_deleting_record()
    {
        Event::fake([ModelHasUpdated::class]);

        User::find(1)->delete();

        Event::assertDispatched(ModelHasUpdated::class);
    }

    #[Test]
    public function it_fires_model_has_updated_event_on_restoring_deleted_record()
    {
        User::find(2)->delete();

        app('events')->listen(ModelHasUpdated::class, function (ModelHasUpdated $event) {
            $this->assertInstanceOf(ModelHasUpdated::class, $event);

            $model = $event->model();

            $this->assertInstanceOf(User::class, $model);
            $this->assertEquals(2, $model->getKey());
        });

        User::withTrashed()->find(2)->restore();
    }

    #[Test]
    public function it_fires_eloquent_retrieved_event_on_retrieving_record_from_database()
    {
        app('events')->listen('eloquent.retrieved:*', function () {
            $args = func_get_args();

            $model = (count($args) === 2) && is_array($args[1]) ? $args[1][0] : $args[0];

            $this->assertInstanceOf(User::class, $model);
            $this->assertEquals(2, $model->getKey());
        });

        User::find(2);
    }

    #[Test]
    public function it_fires_eloquent_wakeup_event_on_unserializing_model_from_cache()
    {
        app('events')->listen('eloquent.wakeup:*', function () {
            $args = func_get_args();

            $model = (count($args) === 2) && is_array($args[1]) ? $args[1][0] : $args[0];

            $this->assertInstanceOf(User::class, $model);
            $this->assertEquals(3, $model->getKey());
        });

        $user = User::find(3);

        $serialized = serialize($user);
        unserialize($serialized);
    }

    #[Test]
    public function it_would_set_last_modified_with_the_newest_updated_at_timestamp()
    {
        $expected = User::orderBy('updated_at', 'desc')->first()->updated_at;

        User::all();

        $actual = \Varnishable::getLastModifiedHeader();

        $this->assertInstanceOf(Carbon::class, $actual);
        $this->assertEquals($expected->getTimestamp(), $actual->getTimestamp());
    }

    #[Test]
    public function it_cant_set_last_modified_when_there_was_no_updated_at_columns_available()
    {
        User::all(['id', 'name', 'email']);

        $actual = \Varnishable::getLastModifiedHeader();

        $this->assertNull($actual);
    }

    #[Test]
    public function serialized_events_can_be_unserialized_without_any_errors_with_soft_deleted_model()
    {
        $user = User::find(3);
        $event = new ModelHasUpdated($user);
        $user->delete();

        $serializedEvent = serialize($event);
        $event = unserialize($serializedEvent);

        $this->assertInstanceOf(ModelHasUpdated::class, $event);

        $event->model();
        $model = $event->model();

        $this->assertInstanceOf(User::class, $model);
        $this->assertEquals(3, $model->getKey());
        $this->assertTrue($model->exists);
        $this->assertCount(0, $model->getDirty());
    }

    #[Test]
    public function serialized_events_can_be_unserialized_without_any_errors_with_deleted_model()
    {
        Post::factory(3)->create();

        $post = Post::find(2);
        $event = new ModelHasUpdated($post);
        $post->delete();

        $serializedEvent = serialize($event);
        $event = unserialize($serializedEvent);

        $this->assertInstanceOf(ModelHasUpdated::class, $event);

        $model = $event->model();

        $this->assertInstanceOf(Post::class, $model);
        $this->assertEquals(2, $model->getKey());
        $this->assertFalse($model->exists);
        $this->assertCount(4, $model->getDirty());
    }
}
