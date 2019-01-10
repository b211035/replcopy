<?php

use Illuminate\Database\Seeder;

class TestSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $project_id = DB::table('projects')->insertGetId(
            [
                'name' => 'デフォルト',
            ]
        );

        $bot_id = DB::table('bots')->insertGetId(
            [
                'project_id' => $project_id,
                'name' => 'サンプル',
                'key' => 'sample',
            ]
        );

        $variable_id = DB::table('variables')->insertGetId(
            [
                'bot_id' => $bot_id,
                'name' => 'name',
                'system' => 0,
            ]
        );

        $scenario_id = DB::table('scenarios')->insertGetId(
            [
                'bot_id' => $bot_id,
                'name' => 'サンプル',
                'key' => 'sample',
                'public' => 1,
            ]
        );

        $start_cell_id = DB::table('scenario_cells')->insertGetId(
            [
                'scenario_id' => $scenario_id,
                'system' => 0,
            ]
        );

        $first_cell_id = DB::table('scenario_cells')->insertGetId(
            [
                'scenario_id' => $scenario_id,
                'system' => 1,
            ]
        );

        // cell_conditions
        // cell_speeches
        // cell_memories
        DB::table('cell_speeches')->insertGetId(
            [
                'scenario_cell_id' => $first_cell_id,
                'text' => 'こんにちは',
            ]
        );

        $second_cell_id = DB::table('scenario_cells')->insertGetId(
            [
                'scenario_id' => $scenario_id,
                'system' => 3,
            ]
        );

        // cell_conditions
        // cell_speeches
        // cell_memories
        DB::table('cell_speeches')->insertGetId(
            [
                'scenario_cell_id' => $second_cell_id,
                'text' => '*',
                'condition' => 0,
            ]
        );

        $therd_cell_id = DB::table('scenario_cells')->insertGetId(
            [
                'scenario_id' => $scenario_id,
                'system' => 1,
            ]
        );

        // cell_conditions
        // cell_speeches
        // cell_memories
        DB::table('cell_speeches')->insertGetId(
            [
                'scenario_cell_id' => $therd_cell_id,
                'text' => 'こんにちは',
            ]
        );

        DB::table('cell_chains')->insert(
            [
                'prev_cell_id' => $start_cell_id,
                'next_cell_id' => $first_cell_id,
            ]
        );
        DB::table('cell_chains')->insert(
            [
                'prev_cell_id' => $first_cell_id,
                'next_cell_id' => $second_cell_id,
            ]
        );
        DB::table('cell_chains')->insert(
            [
                'prev_cell_id' => $second_cell_id,
                'next_cell_id' => $therd_cell_id,
            ]
        );
    }
}
