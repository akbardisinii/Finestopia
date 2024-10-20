@extends('layouts.app')

@section('content')
@auth

<style>
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
        box-shadow: 0 6px 20px rgba(0,0,0,0.2);
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
</style>
<x-header title="" showCreate="false" link="" />
<div class="row m-2 mb-4">
    <div class="col-md-4 mb-4">
        <div class="card dashboard card-border-bottom bg-white h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <h4 class="card-title mb-1" id="total-balance">Rp {{ number_format($totalBalance, 0, ',', '.') }}</h4>
                </div>
                <div class="mt-3">
                    <i class="fas fa-dollar-sign text-primary"></i>
                    <span class="ml-2">Total Saldo</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card dashboard card-border-bottom bg-white h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <h4 class="card-title mb-1" id="total-income">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h4>
                </div>
                <div class="mt-3">
                    <i class="fa fa-long-arrow-up text-success"></i>
                    <span class="ml-2">Total Pemasukan</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card dashboard card-border-bottom bg-white h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <h4 class="card-title mb-1" id="total-expense">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h4>
                </div>
                <div class="mt-3">
                    <i class="fa fa-long-arrow-down text-danger"></i>
                    <span class="ml-2">Total Pengeluaran</span>
                </div>
            </div>
        </div>
    </div>
</div>
   
@include('layouts.session')
<div class="container-lg mb-4">
    <div class="row">
        <!-- Chart yang tidak dipengaruhi oleh paginasi -->
        <div class="col-xl-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Ringkasan Keuangan</h5>
                    <canvas id="bar-index"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Table yang menggunakan paginasi -->
        <div class="col-xl-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Histori Transaksi</h5>
                    <div class="table-responsive">
                        <table class="table table-index table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>Nama Pengeluaran</th>
                                    <th>Jumlah</th>
                                    <th>Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody id="balancesTableBody">
                                @forelse ($balances as $balance)
                                <tr>
                                    <td>{{ ($balances->currentPage() - 1) * $balances->perPage() + $loop->iteration }}</td>
                                    <td>{{ $balance->title }}</td>
                                    <td data-amount="{{ $balance->amount }}">{{ number_format($balance->amount, 0, ',', '.') }}</td>
                                    <td>{{ $balance->description }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada catatan yang ditemukan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination hanya untuk tabel -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $balances->links() }}
                    </div>
                    <a href="{{ route('download.expense.report') }}" class="btn btn-primary mt-3" id="downloadExpenseReport">
                        <i class="fas fa-download mr-1"></i> Unduh Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Action Button -->
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
            <span class="fab-label">Masukkan Pengeluaran</span>
            <div class="fab-icon-holder" id="addExpenseBtn">
                <i class="fas fa-minus"></i>
            </div>
        </li>
    </ul>
</div>

<!-- ADD INCOME MODAL -->
<div class="modal fade" id="addIncomeModal" tabindex="-1" role="dialog" aria-labelledby="addIncomeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addIncomeModalLabel">Add Income</h5>
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

<!-- ADD EXPENSE MODAL -->
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

@else
<script>window.location.href = "{{ route('login') }}";</script>
@endauth
@endsection

@section('script')
@auth
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="{{ asset('js/accounting.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/balance.index.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    console.log('Document ready');

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

    function submitIncome() {
        console.log('submitIncome function called');
        const title = $('#incomeTitle').val();
        const amount = parseFormattedNumber($('#incomeAmount').val());
        const date = $('#incomeDate').val() || null;
        const category = $('#incomeCategory').val() || null;

        if (!title || isNaN(amount)) {
            console.error('Validation error:', { title, amount });
            Swal.fire({
                icon: 'error',
                title: 'Validasi Error',
                text: 'Silakan isi kolom sumber pemasukan dan jumlah dengan benar',
            });
            return;
        }

        console.log('Submitting income:', { title, amount, date, category });

        $.ajax({
            url: '/api/income',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                title: title,
                amount: amount,
                date: date,
                category: category
            },
            success: function(response) {
                console.log('Server response:', response);
                $('#addIncomeModal').modal('hide');
                $('#addIncomeForm')[0].reset();
                
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
                console.error('AJAX error:', status, error);
                console.error('Response Text:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while saving the income: ' + error,
                });
            }
        });
    }

    // Add Income
    $('#addIncomeBtn').click(function() {
        console.log('Add Income button clicked');
        $('#addIncomeModal').modal('show');
    });

    $('#saveIncomeBtn').click(function(e) {
        console.log('Save Income button clicked');
        e.preventDefault();
        submitIncome();
    });

    // Add Expense
    $('#addExpenseBtn').click(function() {
        console.log('Add Expense button clicked');
        $('#AddExpenseModal').modal('show');
    });

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
                Swal.fire({
                    icon: 'success',
                    title: 'Pengeluaran Berhasil Ditambahkan!',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to add expense',
                });
            }
        });
    });

    // Chart
    var ctx = document.getElementById('bar-index').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Income', 'Expense', 'Balance'],
            datasets: [{
                label: 'Ringkasan Keuangan',
                data: [{{ $totalIncome }}, {{ $totalExpense }}, {{ $totalBalance }}],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    function updateTotalExpense(totalExpense) {
        if (totalExpense !== undefined && totalExpense !== null && !isNaN(totalExpense)) {
            $('#total-expenses').text('Rp ' + accounting.formatMoney(totalExpense, "", 0, ".", ","));
            console.log('Total expense updated to:', totalExpense);
        } else {
            console.error('Invalid total expense value:', totalExpense);
        }
    }

    function fetchAndUpdateTotalIncome() {
        $.ajax({
            url: '/api/income/total',
            method: 'GET',
            success: function(data) {
                if (data.totalIncome !== undefined) {
                    $('#total-income').text('Rp ' + accounting.formatMoney(data.totalIncome, "", 0, ".", ","));
                } else {
                    console.error('Invalid total income data received');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching total income:', error);
            }
        });
    }

    function fetchAndUpdateTotalExpense() {
        $.ajax({
            url: '/api/expense/total',
            method: 'GET',
            success: function(data) {
                if (data.totalExpense !== undefined) {
                    updateTotalExpense(data.totalExpense);
                } else {
                    console.error('Invalid total expense data received');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching total expense:', error);
            }
        });
    }

    function fetchAndUpdateBalance() {
        $.ajax({
            url: '/api/balance',
            method: 'GET',
            success: function(data) {
                if (data.balance !== undefined) {
                    $('#total-balance').text('Rp ' + accounting.formatMoney(data.balance, "", 0, ".", ","));
                } else {
                    console.error('Invalid balance data received');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching balance:', error);
            }
        });
    }

    function refreshAllData() {
        fetchAndUpdateTotalIncome();
        fetchAndUpdateTotalExpense();
        fetchAndUpdateBalance();
    }

    function startAutoRefresh() {
        setInterval(refreshAllData, 30000); // Refresh every 30 seconds
    }

    refreshAllData();
    startAutoRefresh();

});

</script>
@endauth
@endsection