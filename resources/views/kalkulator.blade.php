@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Kalkulator Alokasi Keuangan</h2>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Input Alokasi</h5>
                    <form id="allocationCalculatorForm">
                        <div class="form-group">
                            <label for="primaryAllocation">Primary (Rp)</label>
                            <input type="number" class="form-control allocation-input" id="primaryAllocation" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="secondaryAllocation">Secondary (Rp)</label>
                            <input type="number" class="form-control allocation-input" id="secondaryAllocation" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="investmentAllocation">Investment (Rp)</label>
                            <input type="number" class="form-control allocation-input" id="investmentAllocation" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="debtAllocation">Debt (Rp)</label>
                            <input type="number" class="form-control allocation-input" id="debtAllocation" min="0" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Hitung</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Hasil Perhitungan</h5>
                    <div id="allocationResult">
                        <p>Gaji Ideal: <span id="idealSalary">Rp 0</span></p>
                        <p>Primary: <span id="primaryResult">Rp 0</span></p>
                        <p>Secondary: <span id="secondaryResult">Rp 0</span></p>
                        <p>Investment: <span id="investmentResult">Rp 0</span></p>
                        <p>Debt: <span id="debtResult">Rp 0</span></p>
                    </div>
                    <div id="notification" class="alert alert-danger alert-dismissible fade show mt-2" role="alert" style="display: none;">
                        <span id="notificationMessage"></span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    $('#allocationCalculatorForm').on('submit', function(e) {
        e.preventDefault();
        calculateAllocation();
    });

    function calculateAllocation() {
        const primary = Number($('#primaryAllocation').val()) || 0;
        const secondary = Number($('#secondaryAllocation').val()) || 0;
        const investment = Number($('#investmentAllocation').val()) || 0;
        const debt = Number($('#debtAllocation').val()) || 0;

        // Hitung total input user
        const totalInput = primary + secondary + investment + debt;

        // Tentukan apakah ada cicilan atau tidak
        const hasDebt = debt > 0;

        // Proporsi tetap
        let primaryPercentage, secondaryPercentage, investmentPercentage, debtPercentage;
        if (hasDebt) {
            primaryPercentage = 0.40;
            secondaryPercentage = 0.20;
            investmentPercentage = 0.10;
            debtPercentage = 0.30;
        } else {
            primaryPercentage = 0.50;
            secondaryPercentage = 0.20;
            investmentPercentage = 0.30;
            debtPercentage = 0; // Tidak ada cicilan
        }

        // Hitung gaji ideal berdasarkan proporsi tetap
        const idealSalary = totalInput / (primaryPercentage + secondaryPercentage + investmentPercentage + debtPercentage);

        // Hitung kembali nilai alokasi berdasarkan gaji ideal
        const recalculatedPrimary = primaryPercentage * idealSalary;
        const recalculatedSecondary = secondaryPercentage * idealSalary;
        const recalculatedInvestment = investmentPercentage * idealSalary;
        const recalculatedDebt = debtPercentage * idealSalary;

        // Notifikasi jika proporsi tidak sesuai standar
        let notificationMessage = '';
        if (hasDebt) {
            if (
                Math.abs(primary - recalculatedPrimary) > 1 ||
                Math.abs(secondary - recalculatedSecondary) > 1 ||
                Math.abs(investment - recalculatedInvestment) > 1 ||
                Math.abs(debt - recalculatedDebt) > 1
            ) {
                notificationMessage = 'Hasil perhitungan ini berdasarkan proporsi keuangan standar (40%, 20%, 10%, 30%) yang sering digunakan oleh bank. Alokasi Anda saat ini tidak sesuai dengan proporsi standar tersebut.';
            }
        } else {
            if (
                Math.abs(primary - recalculatedPrimary) > 1 ||
                Math.abs(secondary - recalculatedSecondary) > 1 ||
                Math.abs(investment - recalculatedInvestment) > 1
            ) {
                notificationMessage = 'Hasil perhitungan ini berdasarkan proporsi keuangan standar (50%, 20%, 30%) yang sering digunakan oleh bank. Alokasi Anda saat ini tidak sesuai dengan proporsi standar tersebut.';
            }
        }

        // Tampilkan notifikasi jika ada
        if (notificationMessage) {
            $('#notificationMessage').text(notificationMessage);
            $('#notification').show();
        } else {
            $('#notification').hide(); // Sembunyikan jika tidak ada pesan
        }

        // Tampilkan hasil
        $('#idealSalary').text(formatMoney(idealSalary));
        $('#primaryResult').text(formatMoney(recalculatedPrimary));
        $('#secondaryResult').text(formatMoney(recalculatedSecondary));
        $('#investmentResult').text(formatMoney(recalculatedInvestment));
        $('#debtResult').text(formatMoney(recalculatedDebt));
    }

    // Fungsi format untuk menampilkan hasil dalam format uang Indonesia
    function formatMoney(amount) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(amount);
    }
});
</script>
@endsection
