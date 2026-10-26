<?php

use App\Enums\DietsEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! $this->usingPostgres()) {
            return;
        }

        $this->replaceDietCheckConstraint(DietsEnum::cases());
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! $this->usingPostgres()) {
            return;
        }

        DB::table('guests')
            ->where('diet', DietsEnum::STANDARD->value)
            ->update(['diet' => DietsEnum::VEGETARIAN->value]);

        $diets = array_values(array_filter(
            DietsEnum::cases(),
            fn (DietsEnum $diet) => $diet !== DietsEnum::STANDARD,
        ));

        $this->replaceDietCheckConstraint($diets);
    }

    private function usingPostgres(): bool
    {
        return DB::connection()->getDriverName() === 'pgsql';
    }

    /**
     * @param  array<int, DietsEnum>  $diets
     */
    private function replaceDietCheckConstraint(array $diets): void
    {
        $allowedValues = implode(', ', array_map(
            fn (DietsEnum $diet) => DB::connection()->getQueryGrammar()->quoteString($diet->value),
            $diets,
        ));

        DB::statement('ALTER TABLE guests DROP CONSTRAINT IF EXISTS guests_diet_check');
        DB::statement("ALTER TABLE guests ADD CONSTRAINT guests_diet_check CHECK (diet IN ({$allowedValues}))");
    }
};
