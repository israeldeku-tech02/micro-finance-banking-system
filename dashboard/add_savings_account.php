<?php
session_start();
if (empty($_SESSION['user_id'])) { header('Location: ../index.php'); exit; }

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="content">

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h2>Savings Accounts</h2>
            <p class="subtitle">Manage all client savings accounts in one place</p>
        </div>

        <div class="header-actions">
            <input type="text" id="searchAccounts" placeholder="Search by name or phone...">
            <button id="addAccountBtn" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Account
            </button>
        </div>
    </div>

    <!-- Card Wrapper -->
    <div class="card">
        <div class="table-wrapper">
            <table class="table" id="accountsTable">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Daily Savings</th>
                        <th>Start Date</th>
                        <th>Month</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="accountsBody"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL -->
<div id="addAccountModal" class="modal">
    <div class="modal-content">

        <div class="modal-header">
            <h3>Create / Update Savings Account</h3>
            <span class="close">&times;</span>
        </div>

        <form id="addAccountForm">

            <div class="form-grid">

                <div class="field full">
                    <label>Client</label>
                    <select id="clientSearch" name="client_id" required></select>
                </div>

                <div class="field">
                    <label>Daily Amount (₵)</label>
                    <input type="number" step="0.01" min="1" name="daily_amount" required>
                </div>

                <div class="field">
                    <label>Start Date</label>
                    <input type="date" name="start_date" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="field">
                    <label>Savings Month</label>
                    <input type="month" name="savings_month" value="<?= date('Y-m') ?>" required>
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Save Account
                </button>
            </div>

        </form>
    </div>
</div>

<style>

/* ===== PAGE LAYOUT ===== */
.page-header{
    display:flex;
    justify-content:space-between;
    align-items:flex-end;
    margin-bottom:20px;
}

.page-header h2{
    margin:0;
    font-size:22px;
}

.subtitle{
    margin:4px 0 0;
    color:#777;
    font-size:13px;
}

.header-actions{
    display:flex;
    gap:10px;
    align-items:center;
}

.header-actions input{
    padding:10px 12px;
    border:1px solid #ddd;
    border-radius:10px;
    width:260px;
    outline:none;
}

.header-actions input:focus{
    border-color:#2980b9;
}

/* ===== CARD ===== */
.card{
    background:#fff;
    border-radius:14px;
    box-shadow:0 8px 25px rgba(0,0,0,0.06);
    overflow:hidden;
}

/* ===== TABLE ===== */
.table-wrapper{
    overflow:auto;
}

.table{
    width:100%;
    border-collapse:collapse;
    font-size:14px;
}

.table thead{
    background:#f5f7fb;
}

.table th{
    text-align:left;
    padding:14px;
    font-weight:600;
    color:#444;
}

.table td{
    padding:14px;
    border-top:1px solid #eee;
}

.table tbody tr:hover{
    background:#f9fbff;
}

/* ===== BUTTONS ===== */
.btn{
    padding:8px 12px;
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-size:13px;
    display:inline-flex;
    align-items:center;
    gap:6px;
    transition:0.2s;
}

.btn-primary{ background:#2980b9; color:#fff; }
.btn-success{ background:#27ae60; color:#fff; }
.btn-danger{ background:#e74c3c; color:#fff; }

.btn:hover{
    transform:translateY(-1px);
    opacity:0.95;
}

/* ===== MODAL ===== */
.modal{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.55);
    z-index:999;
}

.modal-content{
    background:#fff;
    width:520px;
    margin:80px auto;
    border-radius:16px;
    padding:20px;
    box-shadow:0 15px 40px rgba(0,0,0,0.2);
    animation:pop 0.25s ease;
}

@keyframes pop{
    from{ transform:scale(0.9); opacity:0; }
    to{ transform:scale(1); opacity:1; }
}

.modal-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:15px;
}

.modal-header h3{
    margin:0;
}

.close{
    font-size:22px;
    cursor:pointer;
    color:#777;
}

.close:hover{
    color:#000;
}

/* ===== FORM ===== */
.form-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:12px;
}

.field.full{
    grid-column:1 / -1;
}

.field label{
    font-size:13px;
    font-weight:600;
    margin-bottom:6px;
    display:block;
}

.field input,
.field select{
    width:100%;
    padding:10px;
    border:1px solid #ddd;
    border-radius:10px;
    outline:none;
}

.field input:focus,
.field select:focus{
    border-color:#2980b9;
}

.form-actions{
    margin-top:15px;
    text-align:right;
}

</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

<script>

// modal
const openModal = m => m.style.display='block';
const closeModal = m => m.style.display='none';

document.querySelector('#addAccountBtn').onclick = () =>
    openModal(document.querySelector('#addAccountModal'));

document.querySelectorAll('.close').forEach(x=>{
    x.onclick = () => closeModal(x.closest('.modal'));
});

window.onclick = e=>{
    document.querySelectorAll('.modal').forEach(m=>{
        if(e.target===m) closeModal(m);
    });
};

$(function(){

    $('#clientSearch').select2({
        dropdownParent: $('#addAccountModal'),
        placeholder:'Search client...',
        ajax:{
            url:'../backend/search_clients.php',
            dataType:'json',
            delay:250,
            data: params => ({q:params.term}),
            processResults: data => ({results:data})
        }
    });

    loadAccounts();

    $('#searchAccounts').on('input', function(){
        loadAccounts(this.value);
    });

    $('#addAccountForm').on('submit', function(e){
        e.preventDefault();

        $.post('../backend/add_savings.php', $(this).serialize(), function(resp){
            if(resp.status==='success'){
                closeModal(document.querySelector('#addAccountModal'));
                $('#addAccountForm')[0].reset();
                $('#clientSearch').val(null).trigger('change');
                loadAccounts();
            }else{
                alert(resp.message || 'Error');
            }
        }, 'json');
    });

});

function loadAccounts(q=''){
    $.get('../backend/get_savings_accounts.php',{q}, function(resp){

        const body = $('#accountsBody');
        body.empty();

        if(!resp?.accounts?.length){
            body.html(`<tr><td colspan="5" style="text-align:center;padding:20px;color:#777;">No accounts found</td></tr>`);
            return;
        }

        resp.accounts.forEach(r=>{
            body.append(`
                <tr>
                    <td>
                        <strong>${r.full_name}</strong><br>
                        <small style="color:#777">${r.telephone || ''}</small>
                    </td>
                    <td><strong>₵${r.daily_amount}</strong></td>
                    <td>${r.start_date}</td>
                    <td><span style="background:#eef; padding:4px 8px; border-radius:8px;">${r.savings_month}</span></td>
                    <td>
                        <button class="btn btn-danger" onclick="deleteAccount(${r.id})">
                            <i class="fas fa-times"></i> Close
                        </button>
                    </td>
                </tr>
            `);
        });

    }, 'json');
}

function deleteAccount(id){
    if(!confirm('Close this account?')) return;

    $.post('../backend/delete_savings.php',{savings_id:id}, function(resp){
        if(resp.status==='success') loadAccounts();
        else alert(resp.message || 'Failed');
    }, 'json');
}

</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>