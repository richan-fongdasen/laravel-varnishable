<?php

namespace RichanFongdasen\Varnishable\Tests;

use Carbon\Carbon;
use RichanFongdasen\Varnishable\Events\ModelHasUpdated;
use RichanFongdasen\Varnishable\Tests\Supports\Models\User;

class VarnishableObserverTests extends TestCase
{
    /**
     * Setup the test environment
     */
    public function setUp()
    {
        parent::setUp();

        factory(User::class, 10)->create();
    }

    /** @test */
    public function it_fires_model_has_updated_event_on_creating_new_record()
    {
        $this->expectsEvents(ModelHasUpdated::class);

        $user = factory(User::class)->create();
    }

    /** @test */
    public function it_fires_model_has_updated_event_on_updating_record()
    {
        $this->expectsEvents(ModelHasUpdated::class);

        $user = User::first();
        $user->name = 'Taylor Otwell';
        $user->save();
    }

    /** @test */
    public function it_fires_model_has_updated_event_on_deleting_record()
    {
        $this->expectsEvents(ModelHasUpdated::class);

        User::find(1)->delete();
    }

    /** @test */
    public function it_fires_model_has_updated_event_on_restoring_deleted_record()
    {
        User::find(8)->delete();

        app('events')->listen(ModelHasUpdated::class, function ($event) {
            $this->assertInstanceOf(ModelHasUpdated::class, $event);

            $model = $event->model();

            $this->assertInstanceOf(User::class, $model);
            $this->assertEquals(8, $model->getKey());
        });

        User::withTrashed()->find(8)->restore();
    }

    /** @test */
    public function it_fires_eloquent_retrieved_event_on_retrieving_record_from_database()
    {
        app('events')->listen('eloquent.retrieved:*', function () {
            $args = (array) func_get_args();

            $model = (count($args) == 2) && is_array($args[1]) ? $args[1][0] : $args[0];

            $this->assertInstanceOf(User::class, $model);
            $this->assertEquals(6, $model->getKey());
        });

        User::find(6);
    }

    /** @test */
    public function it_fires_eloquent_wakeup_event_on_unserializing_model_from_cache()
    {
        app('events')->listen('eloquent.wakeup:*', function () {
            $args = (array) func_get_args();

            $model = (count($args) == 2) && is_array($args[1]) ? $args[1][0] : $args[0];

            $this->assertInstanceOf(User::class, $model);
            $this->assertEquals(6, $model->getKey());
        });

        $user = User::find(6);

        $serialized = serialize($user);
        $newUser = unserialize($serialized);
    }

    /** @test */
    public function it_would_set_last_modified_with_the_newest_updated_at_timestamp()
    {
        $expected = User::orderBy('updated_at', 'desc')->first()->updated_at;

        User::all();

        $actual = \Varnishable::getLastModifiedHeader();

        $this->assertInstanceOf(Carbon::class, $actual);
        $this->assertEquals($expected->getTimestamp(), $actual->getTimestamp());
    }

    /** @test */
    public function it_cant_set_last_modified_when_there_was_no_updated_at_columns_available()
    {
        User::all(['id', 'name', 'email']);

        $actual = \Varnishable::getLastModifiedHeader();

        $this->assertNull($actual);
    }
}
