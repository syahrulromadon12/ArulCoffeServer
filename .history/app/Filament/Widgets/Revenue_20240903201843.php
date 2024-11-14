
namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class Revenue extends BaseWidget
{
    protected function getStats(): array
    {
        $totalRevenue = Order::sum('total_price'); // Menghitung total revenue

        // Format Rupiah dengan fungsi number_format
        $formattedRevenue = 'Rp ' . number_format($totalRevenue, 0, ',', '.');

        return [
            Stat::make('Total Revenue', $formattedRevenue)
                ->description('This Month')
                ->descriptionIcon('heroicon-s-currency-dollar')
                ->color('success')
                ->chart([1000000, 2000000, 1500000, 3000000, 4000000, 2500000]), // Contoh data
        ];
    }
}
