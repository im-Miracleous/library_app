<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - Library App</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            line-height: 1.5;
            background: white;
            font-size: 12px;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .header p {
            margin: 5px 0 0;
            color: #64748b;
        }

        .meta {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #475569;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        
        th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.05em;
        }

        tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-berjalan { background: #eff6ff; color: #3b82f6; } /* Blue-50/500 */
        .status-selesai { background: #ecfdf5; color: #10b981; } /* Emerald-50/500 */
        .status-terlambat { background: #fef2f2; color: #ef4444; } /* Red-50/500 */
        .status-menunggu_verifikasi { background: #fffbeb; color: #f59e0b; } /* Amber-50/500 */

        .footer {
            margin-top: 40px;
            text-align: right;
            font-size: 11px;
            color: #64748b;
        }

        .signature {
            margin-top: 50px;
            text-align: right;
        }
        .signature p {
            margin-bottom: 60px;
        }

        @media print {
            @page { 
                size: A4; 
                margin: 20mm; 
            }
            body { 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact; 
            }
            .no-print { display: none; }
            
            /* Prevent rows from splitting across pages */
            tr {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            
            /* Ensure table header repeats but doesn't glitch */
            thead {
                display: table-header-group;
            }
            
            tfoot {
                display: table-footer-group;
            }

            tfoot {
                display: table-footer-group;
            }

            .footer-container {
                page-break-inside: avoid;
                break-inside: avoid;
                margin-top: 40px;
            }

            .footer {
                margin-top: 0; /* Managed by container */
                margin-bottom: 20px;
            }

            .signature {
                margin-top: 0;
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    
    <!-- Print Controls -->
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #0f172a; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
            Cetak Dokumen
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #f1f5f9; color: #475569; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; margin-left: 8px;">
            Tutup
        </button>
    </div>

    <div class="container">
        <div class="header">
            @php
                $titles = [
                    'transaksi' => 'Laporan Transaksi Perpustakaan',
                    'denda' => 'Laporan Denda & Sanksi',
                    'kunjungan' => 'Laporan Kunjungan Perpustakaan',
                    'inventaris' => 'Laporan Inventaris Buku',
                    'buku_top' => 'Laporan Buku Terpopuler',
                    'anggota_top' => 'Laporan Anggota Teraktif',
                ];
            @endphp
            <h1>{{ $titles[$type] ?? 'Laporan Perpustakaan' }}</h1>
            
            @if($type != 'inventaris')
                <p>Periode: {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM Y') }} - {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM Y') }}</p>
            @else
                <p>Dicetak pada: {{ now()->isoFormat('D MMMM Y') }}</p>
            @endif
        </div>

        <div class="meta">
            <div>
                <strong>Dicetak oleh:</strong> {{ auth()->user()->nama ?? 'Administrator' }}<br>
                <strong>Tanggal Cetak:</strong> {{ now()->isoFormat('D MMMM Y HH:mm') }}
            </div>
            <div style="text-align: right">
                <strong>Total Data:</strong> {{ count($data) }}
            </div>
        </div>

        @if($type == 'transaksi')
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 15%">ID Transaksi</th>
                    <th style="width: 20%">Anggota</th>
                    <th style="width: 30%">Buku Dipinjam</th>
                    <th style="width: 15%">Tanggal</th>
                    <th style="width: 15%">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-family: monospace; font-weight: 600;">{{ $item->id_peminjaman }}</td>
                    <td>
                        {{ $item->nama_anggota }}<br>
                        <span style="color: #64748b; font-size: 10px;">{{ $item->email_anggota }}</span>
                    </td>
                    <td>{{ $item->daftar_buku }}</td>
                    <td>
                        Pinjam: {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}<br>
                        <span style="color: #64748b;">Tempo: {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/Y') }}</span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $item->status_transaksi }}">
                            {{ str_replace('_', ' ', $item->status_transaksi) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align: center; padding: 40px; color: #64748b;">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>

        @elseif($type == 'denda')
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 15%">ID Denda</th>
                    <th style="width: 20%">Anggota</th>
                    <th style="width: 25%">Keterangan</th>
                    <th style="width: 15%">Tanggal Kembali</th>
                    <th style="width: 10%">Jumlah</th>
                    <th style="width: 10%">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-family: monospace; font-weight: 600;">{{ $item->id_denda }}</td>
                    <td>{{ $item->nama_anggota }}</td>
                    <td>
                        <span style="font-weight: 600;">{{ ucfirst($item->jenis_denda) }}</span><br>
                        <span style="color: #64748b; font-size: 10px;">{{ $item->keterangan }}</span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') }}</td>
                    <td style="font-weight: 600;">Rp {{ number_format($item->jumlah_denda, 0, ',', '.') }}</td>
                    <td>
                        <span class="status-badge {{ $item->status_bayar == 'lunas' ? 'status-selesai' : 'status-terlambat' }}">
                            {{ ucfirst(str_replace('_', ' ', $item->status_bayar)) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align: center; padding: 40px; color: #64748b;">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>

        @elseif($type == 'kunjungan')
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 20%">Tanggal</th>
                    <th style="width: 25%">Nama Pengunjung</th>
                    <th style="width: 20%">Kategori</th>
                    <th style="width: 30%">Keperluan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}<br>
                        <span style="color: #64748b; font-size: 10px;">{{ \Carbon\Carbon::parse($item->tanggal)->format('H:i') }} WIB</span>
                    </td>
                    <td style="font-weight: 600;">{{ $item->nama_pengunjung }}</td>
                    <td>{{ $item->jenis_pengunjung }}</td>
                    <td>{{ $item->keperluan }}</td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align: center; padding: 40px; color: #64748b;">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>

        @elseif($type == 'inventaris')
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 15%">ISBN</th>
                    <th style="width: 35%">Judul Buku</th>
                    <th style="width: 20%">Kategori</th>
                    <th style="width: 10%">Total</th>
                    <th style="width: 15%">Stok Info</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-family: monospace;">{{ $item->isbn }}</td>
                    <td>
                        <span style="font-weight: 600;">{{ $item->judul }}</span><br>
                        <span style="color: #64748b; font-size: 10px;">{{ $item->penulis }}</span>
                    </td>
                    <td>{{ $item->kategori }}</td>
                    <td style="font-weight: 600; text-align: center;">{{ $item->stok_total }}</td>
                    <td>
                        <div style="font-size: 10px;">
                            <span style="color: #10b981;">Tersedia: {{ $item->stok_tersedia }}</span><br>
                            <span style="color: #f59e0b;">Rusak: {{ $item->stok_rusak }}</span><br>
                            <span style="color: #ef4444;">Hilang: {{ $item->stok_hilang }}</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align: center; padding: 40px; color: #64748b;">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>

        @elseif($type == 'buku_top')
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 60%">Judul Buku</th>
                    <th style="width: 20%">Penulis</th>
                    <th style="width: 15%">Total Dipinjam</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: 600;">{{ $item->judul }}</td>
                    <td>{{ $item->penulis }}</td>
                    <td style="font-weight: 600; text-align: center; font-size: 14px; color: #3b82f6;">{{ $item->total_dipinjam }}x</td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align: center; padding: 40px; color: #64748b;">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>

        @elseif($type == 'anggota_top')
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 50%">Nama Anggota</th>
                    <th style="width: 30%">Email</th>
                    <th style="width: 15%">Total Transaksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: 600;">{{ $item->nama_anggota }}</td>
                    <td style="color: #64748b;">{{ $item->email_anggota }}</td>
                    <td style="font-weight: 600; text-align: center; font-size: 14px; color: #8b5cf6;">{{ $item->total_transaksi }}x</td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align: center; padding: 40px; color: #64748b;">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
        @endif

        <div class="footer-container">
            <div class="footer">
                <p>Dokumen ini dibuat secara otomatis oleh sistem.</p>
            </div>
            
            @if($withSignature)
            <div class="signature">
                <p>Mengetahui,<br>Kepala Perpustakaan</p>
                <br>
                <p><strong>( _______________________ )</strong></p>
            </div>
            @endif
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
