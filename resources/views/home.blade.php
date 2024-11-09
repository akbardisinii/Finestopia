@extends('layouts.app')

@section('content')
<x-header title="Selamat Datang, {{ Auth::user()->name }}!" showCreate="false" link="" />

<style>
    .ai-chat-popup {
        position: fixed;
        right: 20px;
        bottom: 20px;
        width: 60px;
        height: 60px;
        background-color: #007bff;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }

    .ai-chat-popup:hover {
        transform: scale(1.1);
    }

    .ai-chat-popup i {
        color: white;
        font-size: 24px;
    }

    .ai-chat-modal {
        display: none;
        position: fixed;
        right: 20px;
        bottom: 90px;
        width: 300px;
        height: 400px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        z-index: 1000;
    }

    .ai-chat-header {
        background-color: #007bff;
        color: white;
        padding: 10px;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .ai-chat-close {
        cursor: pointer;
    }

    .ai-chat-messages {
        height: 300px;
        overflow-y: auto;
        padding: 10px;
    }

    .ai-chat-input {
        display: flex;
        padding: 10px;
    }

    .ai-chat-input input {
        flex-grow: 1;
        margin-right: 10px;
    }
    .chart-container {
        position: relative;
        height: 50vh;
        width: 100%;
    }
    .fab-container {
        position: fixed;
        bottom: 50px;
        right: 50px;
        z-index: 999;
        cursor: pointer;
    }
    .fab-icon-holder {
        width: 50px;
        height: 50px;
        border-radius: 100%;
        background: #016fb9;
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }
    .fab-icon-holder:hover {
        opacity: 0.8;
    }
    .fab-icon-holder i {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        font-size: 25px;
        color: #ffffff;
    }
    .fab-options {
        list-style-type: none;
        margin: 0;
        position: absolute;
        bottom: 70px;
        right: 0;
        opacity: 0;
        transition: all 0.3s ease;
        transform: scale(0);
        transform-origin: 85% bottom;
    }
    .fab:hover + .fab-options, .fab-options:hover {
        opacity: 1;
        transform: scale(1);
    }
    .fab-options li {
        display: flex;
        justify-content: flex-end;
        padding: 5px;
    }
    .fab-label {
        padding: 2px 5px;
        align-self: center;
        user-select: none;
        white-space: nowrap;
        border-radius: 3px;
        font-size: 16px;
        background: #666666;
        color: #ffffff;
        box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        margin-right: 10px;
    }
    .custom-swal-button {
        margin: 0 5px !important;
    }
    .custom-swal-actions {
        display: flex !important;
        justify-content: center !important;
        gap: 10px !important;
    }
    .btn-circle.btn-xl {
        width: 70px;
        height: 70px;
        padding: 10px 16px;
        border-radius: 50%;
        font-size: 24px;
        line-height: 1.33;
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
    }
    .welcome-text {
        position: relative;
        margin-bottom: 20px;
    }
    .welcome-text button {
        margin-top: 10px;
        font-size: 12px;
    }
    .icon-small {
        font-size: 2.5rem !important;
    }
</style>

<div class="row m-2 mb-4">
    <div class="col-md-4 mb-4">
        <div class="card dashboard card-border-bottom rounded-sm bg-white mb-3 h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <h4 class="card-title mb-1">
                        <h4>Rp {{ number_format($totalIncome, 0, ',', '.') }}</h4>
                    </h4>
                </div>
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <i class="fa fa-long-arrow-up text-success icon-small"></i>
                    <span class="flex-grow-1 ml-2">Total Pemasukan</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card dashboard card-border-bottom bg-white mb-3 h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <h4 class="card-title mb-1">
                        <h4>Rp {{ number_format($totalExpense, 0, ',', '.') }}</h4>
                    </h4>
                </div>
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <i class="fa fa-long-arrow-down text-danger icon-small"></i>
                    <span class="flex-grow-1 ml-2">Total Pengeluaran</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card dashboard card-border-bottom bg-white mb-3 h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <h4 class="card-title mb-1" id="current-date"></h4>
                </div>
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <i class="fa fa-calendar text-primary icon-small"></i>
                    <span class="flex-grow-1 ml-2">Tanggal Hari Ini</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row m-2">
    <!-- Card Kiri: Monthly Summary -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <i class="fa fa-adjust text-primary mr-2"></i>
                    <strong>Pemasukan Bulanan</strong>
                </div>
                <select id="summary-month" class="form-control w-auto d-inline-block">
                </select>
            </div>
            <div class="card-body d-flex flex-column">
                <div style="flex-grow: 1; min-height: 300px;">
                    <canvas id="monthly-summary-chart"></canvas>
                </div>
                <button id="downloadReport" class="btn btn-sm btn-primary">
                    <i class="fas fa-download mr-1"></i> Unduh Laporan
                </button>
            </div>
        </div>
    </div>
    

    <!-- Card Kanan: Alokasi Keuangan -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <i class="fa fa-adjust text-primary mr-2"></i>
                <strong>Alokasi Keuangan</strong>
            </div>
            <div class="card-body">
                <div id="allocation-display"></div>

                <div id="income-info" class="mb-3">
                    <strong>Total Saldo: </strong><span id="total-income-display">Rp. {{ number_format($balance ?? 0, 0, ',', '.') }}</span>
                    <br>
                    <strong>Tipe Alokasi: </strong><span id="allocation-type">{{ $allocation['type'] ?? '' }}</span>
                </div>

                <!-- Progress Bar for Primary -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-home text-success mr-2"></i>
                        <span>Primer</span>
                    </div>
                    <span id="primary-amount">Rp. {{ number_format($allocation['allocations']['primary']['amount'] ?? 0, 0, ',', '.') }} ({{ intval($allocation['allocations']['primary']['percentage'] ?? 0) }}%)</span>
                </div>
                <div class="progress mb-3" style="height: 10px;">
                    <div id="primary-progress" class="progress-bar bg-success" role="progressbar" style="width: {{ $allocation['allocations']['primary']['percentage'] ?? 0 }}%;" aria-valuenow="{{ $allocation['allocations']['primary']['percentage'] ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <!-- Progress Bar for Secondary -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-user text-info mr-2"></i>
                        <span>Sekunder</span>
                    </div>
                    <span id="secondary-amount">Rp. {{ number_format($allocation['allocations']['secondary']['amount'] ?? 0, 0, ',', '.') }} ({{ $allocation['allocations']['secondary']['percentage'] ?? 0 }}%)</span>
                </div>
                <div class="progress mb-3" style="height: 10px;">
                    <div id="secondary-progress" class="progress-bar bg-info" role="progressbar" style="width: {{ $allocation['allocations']['secondary']['percentage'] ?? 0 }}%;" aria-valuenow="{{ $allocation['allocations']['secondary']['percentage'] ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <!-- Progress Bar for Investment -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-briefcase text-warning mr-2"></i>
                        <span>Investasi</span>
                    </div>
                    <span id="investment-amount">Rp. {{ number_format($allocation['allocations']['investment']['amount'] ?? 0, 0, ',', '.') }} ({{ intval($allocation['allocations']['investment']['percentage'] ?? 0) }}%)</span>
                </div>
                <div class="progress mb-3" style="height: 10px;">
                    <div id="investment-progress" class="progress-bar bg-warning" role="progressbar" style="width: {{ $allocation['allocations']['investment']['percentage'] ?? 0 }}%;" aria-valuenow="{{ $allocation['allocations']['investment']['percentage'] ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <!-- Progress Bar for Debt -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-credit-card text-danger mr-2"></i>
                        <span>Cicilan</span>
                    </div>
                    <span id="debt-amount">Rp. {{ number_format($allocation['allocations']['debt']['amount'] ?? 0, 0, ',', '.') }} ({{ intval($allocation['allocations']['debt']['percentage'] ?? 0) }}%)</span>
                </div>
                <div class="progress" style="height: 10px;">
                    <div id="debt-progress" class="progress-bar bg-danger" role="progressbar" style="width: {{ $allocation['allocations']['debt']['percentage'] ?? 0 }}%;" aria-valuenow="{{ $allocation['allocations']['debt']['percentage'] ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                @if(isset($allocation['notif']) && $allocation['notif'])
                <div class="alert alert-warning alert-dismissible fade show mt-2" role="alert">
                <strong>Perhatian!</strong> {{ $allocation['notif'] }}
                </div>
                @endif
                <div class="mt-3">
        <button id="downloadAllocationPdf" class="btn btn-sm btn-primary">
            <i class="fas fa-download mr-1"></i> Unduh Alokasi PDF
        </button>
        <button id="showAllocationHistory" class="btn btn-sm btn-info ml-2">
        <i class="fas fa-history mr-1"></i> Lihat Riwayat Alokasi
    </button>
    </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="AddExpenseModal" tabindex="-1" role="dialog" aria-labelledby="AddExpenseModalBtn" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="AddExpenseModalBtn">Masukkan Pengeluaran</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="addExpenseForm">
          <div class="form-group">
            <label for="expenseTitle">Nama Pengeluaran</label>
            <small class="form-text text-muted" style="display: block; margin-top: -5px; font-size: 0.875rem; color: #6c757d;">Contoh: 'Belanja Bulanan', 'Biaya Listrik'.</small>
            <input type="text" class="form-control" id="expenseTitle" required>
          </div>
          <div class="form-group">
            <label for="expenseAmount">Jumlah</label>
            <input type="text" class="form-control" id="expenseAmount" required>
          </div>
          <div class="form-group">
            <label for="expenseDescription">Deskripsi</label>
            <small class="form-text text-muted" style="display: block; margin-top: -5px; font-size: 0.875rem; color: #6c757d;">Masukkan keterangan tambahan tentang pengeluaran ini.</small>
            <textarea class="form-control" id="expenseDescription" rows="3" required></textarea>
          </div>
          <div class="form-group">
            <label for="expenseDate">Tanggal</label>
            <input type="date" class="form-control" id="expenseDate" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-primary" id="saveExpenseBtn">Simpan Pengeluaran</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Riwayat Alokasi -->
<div class="modal fade" id="allocationHistoryModal" tabindex="-1" aria-labelledby="allocationHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-white border-b">
                <h5 class="modal-title" id="allocationHistoryModalLabel">Riwayat Alokasi Keuangan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Primer</th>
                                <th>Sekunder</th>
                                <th>Investasi</th>
                                <th>Cicilan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="allocationHistoryTable">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Income Modal -->
<div class="modal fade" id="addIncomeModal" tabindex="-1" role="dialog" aria-labelledby="addIncomeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addIncomeModalLabel">Masukan Pendapatan </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addIncomeForm">
                    <div class="form-group">
                        <label for="incomeTitle">Sumber Pendapatan</label>
                        <small class="form-text text-muted" style="display: block; margin-top: -5px; font-size: 0.875rem; color: #6c757d;">Contoh: 'Gaji', 'Bonus'.</small>
                        <input type="text" class="form-control" id="incomeTitle" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="incomeAmount">Jumlah</label>
                        <input type="text" class="form-control" id="incomeAmount" name="amount" required>
                    </div>
                    <div class="form-group">
                        <label for="incomeDate">Tanggal</label>
                        <input type="date" class="form-control" id="incomeDate" name="date" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="saveIncomeBtn">Simpan Pendapatan</button>
            </div>
        </div>
    </div>
</div>

<footer class="footer mt-2" style="position: relative; bottom: 0; left: 0; width: 100%; padding: 10px 0; background-color: #f8f9fa;">
    <div class="w-100" style="text-align: center;">
        <small>Didesain &amp; Dikembangkan oleh Tim Finest</small>
    </div>
</footer>

<!-- Add Income Button -->
<div class="fab-container">
    <div class="fab fab-icon-holder">
        <i class="fas fa-plus"></i>
    </div>
    <ul class="fab-options">
        <li>
            <span class="fab-label">Masukan Pendapatan</span>
            <div class="fab-icon-holder" id="addIncomeBtn">
                <i class="fas fa-plus"></i>
            </div>
        </li>
        <li>
            <span class="fab-label">Masukan Pengeluaran</span>
            <div class="fab-icon-holder" id="addExpenseBtn">
                <i class="fas fa-minus"></i>
            </div>
        </li>
    </ul>
</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    function downloadAllocationHistory(date, total, primary, secondary, investment, debt) {
    // console.log('Downloading history allocation:', { date, total, primary, secondary, investment, debt });

    Swal.fire({
        title: 'Mengunduh PDF...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '/api/allocation/download-pdf',
        method: 'POST',
        data: {
            date: date,
            total: total,
            primary_percentage: primary,
            secondary_percentage: secondary,
            investment_percentage: investment,
            debt_percentage: debt,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        xhrFields: {
            responseType: 'blob'
        },
        success: function(response, status, xhr) {
            Swal.close();
            const contentType = xhr.getResponseHeader('content-type');
            if (contentType && contentType.includes('application/json')) {
                const reader = new FileReader();
                reader.onload = function() {
                    try {
                        const result = JSON.parse(this.result);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: result.message || 'Gagal mengunduh PDF'
                        });
                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal mengunduh PDF'
                        });
                    }
                };
                reader.readAsText(response);
                return;
            }

            // Download PDF
            const blob = new Blob([response], { type: 'application/pdf' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `alokasi_keuangan_${date}.pdf`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        },
        error: function(xhr, status, error) {
            Swal.close();
            // console.error('Error:', { status: xhr.status, statusText: xhr.statusText, error: error });
            
            // error message bang
            try {
                const errorText = xhr.responseText;
                const errorData = JSON.parse(errorText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorData.message || 'Gagal mengunduh PDF'
                });
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengunduh PDF. Silakan coba lagi.'
                });
            }
        }
    });
}
$(document).ready(function() {
    // console.log('Document ready');

    @if(session("status"))
        const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: "btn btn-success custom-swal-button",
            cancelButton: "btn btn-danger custom-swal-button",
            actions: 'custom-swal-actions'
          },
          buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
          title: "Sudah sehatkah pengelolaan keuangan Anda?",
          icon: "question",
          showCancelButton: true,
          confirmButtonText: "Ya, sehat",
          cancelButtonText: "Tidak, belum sehat",
          reverseButtons: false
        }).then((result) => {
          if (result.isConfirmed) {
            swalWithBootstrapButtons.fire({
              title: "Selamat!",
              text: "Keuangan Anda sehat!",
              icon: "success"
            });
          } else if (
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire({
              title: "Tips Keuangan Sehat",
              html: `
                <ul style="text-align: left;">
                  <li>Buatlah anggaran bulanan dan patuhi.</li>
                  <li>Sisihkan minimal 20% pendapatan untuk tabungan.</li>
                  <li>Hindari hutang konsumtif, prioritaskan kebutuhan.</li>
                  <li>Mulailah berinvestasi untuk masa depan.</li>
                  <li>Tingkatkan literasi keuangan Anda.</li>
                </ul>
                              `,
              icon: "info"
            });
          }
        });
    @endif()


    function loadAllocationHistory() {
    // console.log('Loading allocation history...');
    const userId = {{ Auth::id() }}; // user id ye
    const storageKey = `allocationHistory_${userId}`;
    const allocationHistory = JSON.parse(localStorage.getItem(storageKey) || '[]');
    const tableBody = $('#allocationHistoryTable');
    tableBody.empty();

    if (allocationHistory.length === 0) {
        tableBody.append(`
            <tr>
                <td colspan="7" class="text-center">Belum ada riwayat alokasi</td>
            </tr>
        `);
    } else {
        allocationHistory.reverse().forEach((allocation) => {
            // allocation current user
            if (allocation.user_id === userId) {
                try {
                    const dateParts = allocation.date.split('-');
                    const formattedDate = `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`;
                    
                    // Pastikan semua nilai adalah number
                    const total = Number(allocation.total || allocation.balance);
                    const primary = Number(allocation.primary_percentage);
                    const secondary = Number(allocation.secondary_percentage);
                    const investment = Number(allocation.investment_percentage);
                    const debt = Number(allocation.debt_percentage);

                    // nominal e
                    const primaryAmount = Math.floor(total * primary / 100);
                    const secondaryAmount = Math.floor(total * secondary / 100);
                    const investmentAmount = Math.floor(total * investment / 100);
                    const debtAmount = Math.floor(total * debt / 100);

                    const row = `
                        <tr>
                            <td>${formattedDate}</td>
                            <td>Rp ${new Intl.NumberFormat('id-ID').format(total)}</td>
                            <td>Rp ${new Intl.NumberFormat('id-ID').format(primaryAmount)} (${primary}%)</td>
                            <td>Rp ${new Intl.NumberFormat('id-ID').format(secondaryAmount)} (${secondary}%)</td>
                            <td>Rp ${new Intl.NumberFormat('id-ID').format(investmentAmount)} (${investment}%)</td>
                            <td>Rp ${new Intl.NumberFormat('id-ID').format(debtAmount)} (${debt}%)</td>
                            <td>
                                <button class="btn btn-sm btn-primary" 
                                    onclick="downloadAllocationHistory('${allocation.date}', ${total}, ${primary}, ${secondary}, ${investment}, ${debt})">
                                    <i class="fas fa-download"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tableBody.append(row);
                } catch (error) {
                    // console.error('Error processing allocation row:', error);
                }
            }
        });
    }
    
    $('#allocationHistoryModal').modal('show');
}


function saveNewAllocation(balance) {
    const newAllocation = {
        id: Date.now(),
        user_id: {{ Auth::id() }}, // Add user ID
        date: new Date().toISOString().split('T')[0],
        total: Number(balance),
        primary_percentage: Number($('#primary-progress').attr('aria-valuenow')),
        secondary_percentage: Number($('#secondary-progress').attr('aria-valuenow')),
        investment_percentage: Number($('#investment-progress').attr('aria-valuenow')),
        debt_percentage: Number($('#debt-progress').attr('aria-valuenow'))
    };
    
    // console.log('Saving new allocation:', newAllocation);
    
    const storageKey = `allocationHistory_${newAllocation.user_id}`; // User-specific storage key
    const allocationHistory = JSON.parse(localStorage.getItem(storageKey) || '[]');
    allocationHistory.push(newAllocation);
    localStorage.setItem(storageKey, JSON.stringify(allocationHistory));
}
// Event listener riwayat alokasi
$('#showAllocationHistory').on('click', function() {
    loadAllocationHistory();
});
// Event listener download riwayat alokasi
$(document).on('click', '.download-allocation', function() {
    const allocationId = $(this).data('id');
    if (allocationId) {
        window.location.href = `/api/allocation/${allocationId}/pdf`;
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'ID alokasi tidak valid'
        });
    }
});

function formatNumber(number) {
    try {
        // number onli bang
        const num = typeof number === 'string' ? parseFloat(number.replace(/[^\d.-]/g, '')) : number;
        if (isNaN(num)) return '0';
        return new Intl.NumberFormat('id-ID').format(num);
    } catch (error) {
        // console.error('Error formatting number:', error);
        return '0';
    }
}

    $('#downloadAllocationPdf').on('click', function(e) {
    e.preventDefault();
    // console.log('Download allocation PDF button clicked');
    
    var url = '{{ route("allocation.pdf") }}';
    url = url.replace(/^http:/, 'https:');
    
    $.ajax({
        url: url,
        method: 'GET',
        xhrFields: {
            responseType: 'blob'
        },
        success: function(data) {
            var blob = new Blob([data], {type: 'application/pdf'});
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = 'financial_allocation.pdf';
            link.click();
        },
        error: function(xhr, status, error) {
            // console.error('Error downloading PDF:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to download the PDF. Please try again later.',
            });
        }
    });
});

    function updateCurrentDate() {
      const now = new Date();
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      const formattedDate = now.toLocaleDateString('id-ID', options);
       $('#current-date').html(`<small>${formattedDate}</small>`);
    }
    updateCurrentDate();
    setInterval(updateCurrentDate, 60000);

    function showAddIncomeModal() {
    //   console.log('showAddIncomeModal called');
      $('#addIncomeModal').modal('show');
    }

    function showAddSalaryModal() {
      $('#addSalaryModal').modal('show');
    }

    function formatNumber(n) {
        return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    
    function parseFormattedNumber(n) {
        return parseFloat(n.replace(/\./g, ''));
    }

    $('#incomeAmount').on('input', function() {
        $(this).val(formatNumber($(this).val()));
    });

    $('#expenseAmount').on('input', function() {
        $(this).val(formatNumber($(this).val()));
    });

    function showAddExpenseModal() {
        $('#AddExpenseModal').modal('show');
    }

    // Add Expense
    $('#addExpenseBtn').click(showAddExpenseModal);

    $('#saveExpenseBtn').click(function() {
    var expenseData = {
        title: $('#expenseTitle').val(),
        amount: parseFormattedNumber($('#expenseAmount').val()),
        description: $('#expenseDescription').val(),
        date: $('#expenseDate').val()
    };

    $.ajax({
        url: '/api/expenses',
        method: 'POST',
        data: expenseData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $('#AddExpenseModal').modal('hide');
            
            // Check if the balance negative
            if (response.balance < 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Saldo Anda tidak cukup. Balance saat ini: Rp ' + formatNumber(response.balance),
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                const currentBalance = parseFloat($('#total-income-display').text().replace('Rp. ', '').replace(/\./g, ''));
            
            saveNewAllocation(currentBalance);
                Swal.fire({
                    icon: 'success',
                    title: 'Pengeluaran Berhasil Ditambahkan!',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.reload();
                });
            }
        },
        error: function(xhr) {
           if (xhr.status === 400) {
               Swal.fire({
                   icon: 'error',
                   title: 'Saldo Tidak Cukup',
                   text: xhr.responseJSON.message
               });
           } else {
               Swal.fire({
                   icon: 'error',
                   title: 'Error',
                   text: 'Gagal Menambahkan Pengeluaran',
               });
           }
       }
    });
});

    $('#downloadReport').on('click', function() {
    // console.log('Download button clicked');
    const selectedMonth = $('#summary-month').val();
    // console.log('Selected month:', selectedMonth);
    window.location.href = `/api/income/monthly-report/${selectedMonth}`;
    
});



function submitIncome() {
    // console.log('submitIncome function called');
    const title = $('#incomeTitle').val();
    const amount = parseFormattedNumber($('#incomeAmount').val());
    const date = $('#incomeDate').val() || null;

    if (!title || isNaN(amount)) {
        // console.error('Validation error:', { title, amount });
        Swal.fire({
            icon: 'error',
            title: 'Validasi Error',
            text: 'Silakan isi kolom sumber pemasukan dan jumlah dengan benar',
        });
        return;
    }

    $.ajax({
        url: '/api/income',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            title: title,
            amount: amount,
            date: date
        },
        success: function(response) {
            // console.log('Server response:', response);
            $('#addIncomeModal').modal('hide');
            $('#addIncomeForm')[0].reset();
            
            // Ambil balance terapdet wkwkwk
            const currentBalance = parseFloat($('#total-income-display').text().replace('Rp. ', '').replace(/\./g, ''));
            
            saveNewAllocation(currentBalance);

            Swal.fire({
                icon: 'success',
                title: 'Pemasukan Berhasil Ditambahkan!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.reload();
            });
        },
        error: function(xhr, status, error) {
            // console.error('AJAX error:', status, error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat menyimpan pemasukan.',
            });
        }
    });
}

    function fetchAndUpdateBalance() {
      $.ajax({
        url: '/api/balance',
        method: 'GET',
        success: function(data) {
          //console.log('Received balance data:', data);
          $('#total-income-display').text(data.formattedBalance);
        },
        error: function(xhr, status, error) {
        //  console.error('Error fetching balance:', error);
        }
      });
    }

    function populateMonthSelect() {
        const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                            "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        const currentDate = new Date();
        const currentMonth = currentDate.getMonth();
        const currentYear = currentDate.getFullYear();

        for (let i = 0; i < 12; i++) {
            const monthIndex = i;
            const value = `${currentYear}-${(monthIndex + 1).toString().padStart(2, '0')}`;
            const text = `${monthNames[monthIndex]} ${currentYear}`;
            const option = $('<option>', {
                value: value,
                text: text
            });
            
            if (i === currentMonth) {
                option.prop('selected', true);
            }
            
            $('#summary-month').append(option);
        }
    }

    function fetchMonthlySummary(yearMonth) {
    // console.log('Fetching monthly summary for:', yearMonth);
    $.ajax({
        url: `/api/income/monthly-summary/${yearMonth}`,
        method: 'GET',
        success: function(data) {
            // console.log('Received monthly summary data:', data);
            if (data && data.labels && data.incomes) {
                updateMonthlySummaryChart(data);
            } else {
                // console.error('Invalid data structure received from API');
            }
        },
        error: function(xhr, status, error) {
            // console.error('Error fetching monthly income summary:', error);
            // console.error('Response:', xhr.responseText);
        }
    });
}

function updateMonthlySummaryChart(data) {
    // console.log('Updating monthly summary chart with data:', data);
    const ctx = document.getElementById('monthly-summary-chart');
    if (!ctx) {
        // console.error('Canvas element "monthly-summary-chart" not found');
        return;
    }

    if (window.monthlySummaryChart instanceof Chart) {
        window.monthlySummaryChart.destroy();
    }

    window.monthlySummaryChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Income',
                data: data.incomes,
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, values) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
}

    populateMonthSelect();
    const currentDate = new Date();
    const currentYearMonth = `${currentDate.getFullYear()}-${(currentDate.getMonth() + 1).toString().padStart(2, '0')}`;
    fetchMonthlySummary(currentYearMonth);

    $('#summary-month').on('change', function() {
    const selectedMonth = $(this).val();
    // console.log('Selected month:', selectedMonth);
    fetchMonthlySummary(selectedMonth);
});

    function fetchAndUpdateTotalIncome() {
        // console.log('Fetching and updating total income...');
        $.ajax({
            url: '/api/income/total',
            method: 'GET',
            success: function(data) {
            },
            error: function(xhr, status, error) {
                // console.error('Error fetching total income:', error);
            }
        });
    }

    function fetchAndUpdateFinancialAllocation() {
        // console.log('Fetching and updating financial allocation...');
        $.ajax({
            url: '/api/allocation',
            method: 'GET',
            success: function(data) {
                // console.log('Received allocation data:', data);
                if (data && data.allocations) {
                    updateFinancialAllocationDisplay(data.allocations);
                } else {
                    // console.error('Invalid allocation data received');
                    displayNoAllocationData();
                }
            },
            error: function(xhr, status, error) {
                // console.error('Error fetching allocation data:', error);
                displayNoAllocationData();
            }
        });
    }

    function updateFinancialAllocationDisplay(allocation) {
        // console.log('Updating financial allocation display:', JSON.stringify(allocation));
        
        const total = parseFloat(allocation.total) || 0;
        $('#total-balance-display').text('Rp. ' + formatMoney(total));

        ['primary', 'secondary', 'investment', 'debt'].forEach(category => {
            // console.log(`Updating ${category}:`, allocation[category]);
        });
        
    }

    

    function formatMoney(amount) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    }

   

    function displayNoAllocationData() {
        // console.warn('Displaying no allocation data');
        ['primary', 'secondary', 'investment', 'debt'].forEach(category => {
            $(`#${category}-amount`).text('Rp. 0 (0%)');
            $(`#${category}-progress`).css('width', '0%').attr('aria-valuenow', 0);
        });
        
    }

    function refreshAllData() {
        fetchAndUpdateTotalIncome();
        fetchAndUpdateTotalExpense();
        fetchAndUpdateFinancialAllocation();
        fetchAndUpdateBalance();
    }

    function fetchAndUpdateTotalExpense() {
        // console.log('Fetching and updating total expense...');
        $.ajax({
            url: '/api/expense/total',
            method: 'GET',
            success: function(data) {
                // console.log('Received total expense:', data);
                if (data.totalExpense !== undefined) {
                    updateTotalExpense(data.totalExpense);
                } else {
                    // console.error('Invalid total expense data received');
                }
            },
            error: function(xhr, status, error) {
                // console.error('Error fetching total expense:', error);
            }
        });
        
    }

    function startAutoRefresh() {
        setInterval(refreshAllData, 30000);
    }

    refreshAllData();
    startAutoRefresh();

    $('#addIncomeBtn').on('click', showAddIncomeModal);
    $('#saveIncomeBtn').on('click', function(e) {
        e.preventDefault();
        submitIncome();
    });
});

function updateTotalExpense(totalExpense) {
    if (totalExpense !== undefined && totalExpense !== null && !isNaN(totalExpense)) {
        $('#total-expenses').text('Rp ' + accounting.formatMoney(totalExpense, "", 0, ".", ","));
        // console.log('Total expense updated to:', totalExpense);
    } else {
        // console.error('Invalid total expense value:', totalExpense);
    }
}


</script>
@endsection