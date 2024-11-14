<?=

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use Carbon\Carbon;

class RecentOrders extends BaseWidget
{
    protected function getStats(): array
    {
        $newOrdersCount = Order::where('created_at', '>=', Carbon::now()->startOfMonth())->count();

        return [
            Stat::make('New Orders', $newOrdersCount)
                ->description('This Month')
                ->descriptionIcon('heroicon-s-shopping-cart')
                ->color('primary')
                ->chart($this->getMonthlyOrdersData()), // Example data
        ];
    }

    private function getMonthlyOrdersData()
    {
        // Mengambil data dinamis untuk grafik
        $data = Order::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Format data sesuai dengan bulan
        $monthlyData = array_fill(1, 12, 0);
        foreach ($data as $month => $count) {
            $monthlyData[$month] = $count;
        }

        return array_values($monthlyData);
    }
}
