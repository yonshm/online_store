<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport des Statistiques</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #333;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h2 {
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            font-size: 16px;
        }
        .stats-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }
        .stat-card {
            border: 1px solid #ddd;
            padding: 10px;
            width: calc(25% - 10px);
            text-align: center;
            background-color: #f9f9f9;
        }
        .stat-card h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #666;
        }
        .stat-card .value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .page-break {
            page-break-before: always;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport des Statistiques</h1>
        <p>Généré le {{ $generatedAt }}</p>
    </div>

    <!-- Statistiques générales -->
    <div class="section">
        <h2>Statistiques Générales</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Chiffre d'affaires total</h3>
                <div class="value">${{ number_format($totalRevenue, 2) }}</div>
            </div>
            <div class="stat-card">
                <h3>Bénéfice total</h3>
                <div class="value">${{ number_format($totalProfit, 2) }}</div>
            </div>
            <div class="stat-card">
                <h3>Total des commandes</h3>
                <div class="value">{{ $totalOrders }}</div>
            </div>
            <div class="stat-card">
                <h3>Total des produits</h3>
                <div class="value">{{ $totalProducts }}</div>
            </div>
        </div>
    </div>

    <!-- Statistiques par période -->
    <div class="section">
        <h2>Statistiques par Période</h2>
        <table>
            <thead>
                <tr>
                    <th>Période</th>
                    <th>Chiffre d'affaires</th>
                    <th>Bénéfice</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Aujourd'hui</td>
                    <td class="text-right">${{ number_format($todayRevenue, 2) }}</td>
                    <td class="text-right">${{ number_format($todayProfit, 2) }}</td>
                </tr>
                <tr>
                    <td>Ce mois</td>
                    <td class="text-right">${{ number_format($thisMonthRevenue, 2) }}</td>
                    <td class="text-right">${{ number_format($thisMonthProfit, 2) }}</td>
                </tr>
                <tr>
                    <td>Cette année</td>
                    <td class="text-right">${{ number_format($thisYearRevenue, 2) }}</td>
                    <td class="text-right">${{ number_format($thisYearProfit, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Chiffre d'affaires par jour -->
    <div class="section">
        <h2>Chiffre d'affaires par Jour (7 derniers jours)</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Chiffre d'affaires</th>
                </tr>
            </thead>
            <tbody>
                @foreach($revenueByDay as $day)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }}</td>
                    <td class="text-right">${{ number_format($day->revenue, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Chiffre d'affaires par mois -->
    <div class="section">
        <h2>Chiffre d'affaires par Mois (12 derniers mois)</h2>
        <table>
            <thead>
                <tr>
                    <th>Mois/Année</th>
                    <th>Chiffre d'affaires</th>
                </tr>
            </thead>
            <tbody>
                @foreach($revenueByMonth as $month)
                <tr>
                    <td>{{ \Carbon\Carbon::createFromDate($month->year, $month->month, 1)->format('m/Y') }}</td>
                    <td class="text-right">${{ number_format($month->revenue, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Chiffre d'affaires par produit -->
    <div class="section">
        <h2>Chiffre d'affaires par Produit</h2>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Chiffre d'affaires</th>
                </tr>
            </thead>
            <tbody>
                @foreach($revenueByProduct as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td class="text-right">${{ number_format($product->revenue, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Bénéfices par produit -->
    <div class="section">
        <h2>Bénéfices par Produit</h2>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Bénéfice</th>
                </tr>
            </thead>
            <tbody>
                @foreach($profitByProduct as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td class="text-right">${{ number_format($product->profit, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Chiffre d'affaires par catégorie -->
    <div class="section">
        <h2>Chiffre d'affaires par Catégorie</h2>
        <table>
            <thead>
                <tr>
                    <th>Catégorie</th>
                    <th>Chiffre d'affaires</th>
                </tr>
            </thead>
            <tbody>
                @foreach($revenueByCategory as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td class="text-right">${{ number_format($category->revenue, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Bénéfices par catégorie -->
    <div class="section">
        <h2>Bénéfices par Catégorie</h2>
        <table>
            <thead>
                <tr>
                    <th>Catégorie</th>
                    <th>Bénéfice</th>
                </tr>
            </thead>
            <tbody>
                @foreach($profitByCategory as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td class="text-right">${{ number_format($category->profit, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Commandes récentes -->
    <div class="section">
        <h2>Commandes Récentes</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Commande</th>
                    <th>Client</th>
                    <th>Total</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentOrders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->user->email }}</td>
                    <td class="text-right">${{ number_format($order->total, 2) }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Rapport généré automatiquement par le système de gestion</p>
    </div>
</body>
</html> 