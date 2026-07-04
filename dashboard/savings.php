<?php 
session_start();
if (empty($_SESSION['user_id'])) { header('Location: ../index.php'); exit; }

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../includes/footer.php';
?>

<div class="content">
    <h2>Daily Savings Management</h2>

    <!-- Top Controls -->
    <div class="top-controls">
        <input type="text" id="searchSavings" placeholder="Search by client name or phone...">
        <div class="right-controls">
            <div class="view-toggle">
                <button id="tabTransactions" class="btn btn-tab active">Transactions</button>
                <button id="tabAccounts" class="btn btn-tab">Accounts Summary</button>
                <button id="tabAdminWithdrawals" class="btn btn-tab">Admin Withdrawals</button>
            </div>
            <button id="addSavingsBtn" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Savings Transaction
            </button>
            <button id="addAdminWithdrawBtn" class="btn btn-success" style="display:none;">
                <i class="fas fa-university"></i> Withdraw Admin Fee
            </button>
        </div>
    </div>

    <!-- Transactions Section -->
    <div id="transactionsSection">
        <table class="table" id="savingsTable">
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Phone</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Balance</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody id="savingsBody"></tbody>
        </table>
    </div>

    <!-- Accounts Summary Section -->
    <div id="accountsSection" style="display:none;">
        <div class="summary-cards">
            <div class="card">
                <div class="card-title">Total Accounts</div>
                <div class="card-value" id="sumAccounts">0</div>
            </div>
            <div class="card">
                <div class="card-title">Pending Admin Fee (eligible)</div>
                <div class="card-value" id="sumAdminFee">₵0.00</div>
            </div>
            <div class="card">
                <div class="card-title">Total Withdrawable (eligible)</div>
                <div class="card-value" id="sumWithdrawable">₵0.00</div>
            </div>
            <div class="card">
                <div class="card-title">Ready to Close</div>
                <div class="card-value" id="sumReady">0</div>
            </div>
        </div>

        <table class="table" id="accountsTable">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Phone</th>
                    <th>Daily Amount</th>
                    <th>Days Paid</th>
                    <th>Clients Total Paid</th>
                    <th>Clients Amount Withdrawable</th>
                    <th>Admin Fee/Per Month (1 Day Cut)</th>
                    <th>Already Withdrawn</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="accountsBody"></tbody>
        </table>
    </div>

    <!-- Admin Withdrawals Section -->
    <div id="adminWithdrawalsSection" style="display:none;">
        <h3>Admin Withdrawals</h3>
        <table class="table" id="adminWithdrawalsTable">
            <thead>
                <tr>
                    <th>Amount</th>
                    <th>Withdrawn At</th>
                    <th>Note</th>
                    <th>Admin</th>
                </tr>
            </thead>
            <tbody id="adminWithdrawalsBody"></tbody>
        </table>
    </div>
</div>

<!-- Modals -->
<div id="addSavingsModal" class="modal">
    <div class="modal-content" style="max-width:650px;">
        <span class="close">&times;</span>
        <h3>Add Savings Transaction</h3>
        <form id="addSavingsForm">
            <div class="form-grid">
                <div class="field full">
                    <label for="clientSearchModal">Client</label>
                    <select id="clientSearchModal" name="client_id" required></select>
                </div>
                <div class="field">
                    <label>Transaction Type</label>
                    <div class="radio-row">
                        <label><input type="radio" name="type" value="deposit" required> Deposit</label>
                        <label><input type="radio" name="type" value="withdrawal"> Withdrawal</label>
                    </div>
                </div>
                <div class="field">
                    <label>Amount</label>
                    <input type="number" name="amount" min="0" step="0.01" required>
                </div>
                <div class="field">
                    <label>Date</label>
                    <input type="date" name="date" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="field full">
                    <label>Details</label>
                    <textarea name="details" rows="3" placeholder="Optional note..."></textarea>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-success">Save Transaction</button>
            </div>
        </form>
    </div>
</div>

<div id="addAdminWithdrawModal" class="modal">
    <div class="modal-content" style="max-width:450px;">
        <span class="close">&times;</span>
        <h3>Withdraw Admin Fee</h3>
        <form id="addAdminWithdrawForm">
            <div class="form-grid">
                <div class="field">
                    <label>Amount</label>
                    <input type="number" name="amount" min="0" step="0.01" required>
                </div>
                <div class="field full">
                    <label>Note</label>
                    <textarea name="note" rows="3" placeholder="Optional note..."></textarea>
                </div>
                <div class="field full">
                    <label>Admin Password</label>
                    <input type="password" name="password" required>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-success">Confirm Withdrawal</button>
            </div>
        </form>
    </div>
</div>

<style>
/* =========================
   GLOBAL THEME RESET
========================= */
:root{
    --primary:#2563eb;
    --success:#16a34a;
    --danger:#ef4444;
    --text:#111827;
    --muted:#6b7280;
    --border:#e5e7eb;
    --bg:#f6f7fb;
    --card:#ffffff;
    --shadow:0 10px 25px rgba(0,0,0,0.06);
}

body{
    background:var(--bg);
    color:var(--text);
    font-family: "Segoe UI", system-ui, -apple-system, sans-serif;
}

/* =========================
   TOP CONTROLS
========================= */
.top-controls{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    margin-bottom:18px;
}

.top-controls input{
    width:42%;
    padding:11px 14px;
    border:1px solid var(--border);
    border-radius:12px;
    background:#fff;
    outline:none;
    transition:0.2s;
    font-size:14px;
}

.top-controls input:focus{
    border-color:var(--primary);
    box-shadow:0 0 0 3px rgba(37,99,235,0.1);
}

/* =========================
   VIEW TABS
========================= */
.btn-tab{
    background:#fff;
    border:1px solid var(--border);
    padding:10px 14px;
    border-radius:12px;
    cursor:pointer;
    font-weight:500;
    color:var(--muted);
    transition:0.2s;
}

.btn-tab:hover{
    border-color:var(--primary);
    color:var(--primary);
}

.btn-tab.active{
    background:var(--primary);
    color:#fff;
    border-color:var(--primary);
}

/* =========================
   BUTTONS
========================= */
.btn{
    padding:10px 14px;
    border:none;
    cursor:pointer;
    border-radius:12px;
    font-size:14px;
    font-weight:500;
    display:inline-flex;
    align-items:center;
    gap:8px;
    transition:0.2s;
}

.btn:hover{
    transform:translateY(-1px);
    opacity:0.95;
}

.btn-primary{ background:var(--primary); color:#fff; }
.btn-success{ background:var(--success); color:#fff; }
.btn-danger{ background:var(--danger); color:#fff; }

/* =========================
   TABLE DESIGN (MODERN)
========================= */
.table{
    width:100%;
    border-collapse:separate;
    border-spacing:0;
    font-size:14px;
    background:#fff;
    border-radius:14px;
    overflow:hidden;
    box-shadow:var(--shadow);
}

.table thead{
    background:#f9fafb;
}

.table th{
    text-align:left;
    padding:14px;
    font-size:12px;
    text-transform:uppercase;
    letter-spacing:.5px;
    color:var(--muted);
    border-bottom:1px solid var(--border);
}

.table td{
    padding:14px;
    border-bottom:1px solid var(--border);
    color:var(--text);
}

.table tbody tr{
    transition:0.2s;
}

.table tbody tr:hover{
    background:#f3f6ff;
}

/* =========================
   SUMMARY CARDS
========================= */
.summary-cards{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(200px,1fr));
    gap:14px;
    margin:14px 0 18px;
}

.summary-cards .card{
    background:var(--card);
    border:1px solid var(--border);
    border-radius:14px;
    padding:16px;
    box-shadow:var(--shadow);
    transition:0.2s;
}

.summary-cards .card:hover{
    transform:translateY(-2px);
}

.card-title{
    font-size:11px;
    color:var(--muted);
    text-transform:uppercase;
    letter-spacing:.6px;
    margin-bottom:6px;
}

.card-value{
    font-size:20px;
    font-weight:700;
}

/* =========================
   MODAL (CLEAN & CENTERED)
========================= */
.modal{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(17,24,39,0.6);
    backdrop-filter: blur(4px);
    z-index:1000;
}

.modal-content{
    background:#fff;
    width:92%;
    max-width:650px;
    margin:70px auto;
    border-radius:16px;
    padding:20px;
    box-shadow:0 20px 50px rgba(0,0,0,0.25);
    animation:modalPop 0.25s ease;
}

@keyframes modalPop{
    from{ transform:scale(0.95); opacity:0; }
    to{ transform:scale(1); opacity:1; }
}

.modal .close{
    position:absolute;
    top:14px;
    right:16px;
    font-size:22px;
    cursor:pointer;
    color:var(--muted);
}

.modal .close:hover{
    color:var(--text);
}

/* =========================
   FORM STYLING
========================= */
.form-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(220px,1fr));
    gap:14px;
}

.form-grid .full{
    grid-column:1 / -1;
}

.field label{
    display:block;
    margin-bottom:6px;
    font-weight:600;
    font-size:13px;
    color:var(--text);
}

.field input,
.field textarea,
.field select{
    width:100%;
    padding:11px 12px;
    border:1px solid var(--border);
    border-radius:12px;
    font-size:14px;
    outline:none;
    transition:0.2s;
    background:#fff;
}

.field input:focus,
.field textarea:focus,
.field select:focus{
    border-color:var(--primary);
    box-shadow:0 0 0 3px rgba(37,99,235,0.1);
}

.radio-row{
    display:flex;
    gap:18px;
    align-items:center;
}

/* =========================
   FORM ACTIONS
========================= */
.form-actions{
    margin-top:16px;
    display:flex;
    justify-content:flex-end;
}

/* =========================
   SMALL UX IMPROVEMENTS
========================= */
h2{
    font-size:22px;
    font-weight:700;
    margin-bottom:4px;
}

h3{
    font-size:18px;
    font-weight:600;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
const qs = (sel, el=document) => el.querySelector(sel);
const qsa = (sel, el=document) => [...el.querySelectorAll(sel)];
const openModal = m => m.style.display='block';
const closeModal = m => m.style.display='none';
const money = v => '₵'+(Number(v||0).toFixed(2));

function activateTab(which){
    const tabs = {
        transactions: {btn:'#tabTransactions', sec:'#transactionsSection'},
        accounts: {btn:'#tabAccounts', sec:'#accountsSection'},
        admin: {btn:'#tabAdminWithdrawals', sec:'#adminWithdrawalsSection'}
    };
    for(const k in tabs){
        qs(tabs[k].btn).classList.remove('active');
        qs(tabs[k].sec).style.display='none';
    }
    qs(tabs[which].btn).classList.add('active');
    qs(tabs[which].sec).style.display='block';
    if(which==='transactions') loadTransactions(qs('#searchSavings').value||'');
    if(which==='accounts') loadAccounts(qs('#searchSavings').value||'');
    if(which==='admin'){
        loadAdminWithdrawals();
        qs('#addAdminWithdrawBtn').style.display='inline-block';
    } else qs('#addAdminWithdrawBtn').style.display='none';
}

qsa('.modal .close').forEach(x=>x.addEventListener('click',()=>closeModal(x.closest('.modal'))));
window.addEventListener('click', e=>{ qsa('.modal').forEach(m=>{ if(e.target===m) closeModal(m); }); });

qs('#addSavingsBtn').addEventListener('click', ()=>openModal(qs('#addSavingsModal')));
qs('#addAdminWithdrawBtn').addEventListener('click', ()=>openModal(qs('#addAdminWithdrawModal')));

qs('#tabTransactions').addEventListener('click', ()=>activateTab('transactions'));
qs('#tabAccounts').addEventListener('click', ()=>activateTab('accounts'));
qs('#tabAdminWithdrawals').addEventListener('click', ()=>activateTab('admin'));

$(function(){
    $('#clientSearchModal').select2({
        dropdownParent: $('#addSavingsModal'),
        placeholder:'Search/select client...',
        width:'100%',
        allowClear:true,
        ajax:{url:'../backend/search_savings_clients.php', dataType:'json', delay:250, data:params=>({q:params.term}), processResults:data=>({results:data})}
    });

    loadTransactions();
    loadAccounts();

    $('#searchSavings').on('input', function(){
        if(qs('#tabTransactions').classList.contains('active')) loadTransactions(this.value);
        else if(qs('#tabAccounts').classList.contains('active')) loadAccounts(this.value);
    });

    // ---------- Add Savings Form ----------
    $('#addSavingsForm').on('submit', function(e){
        e.preventDefault();

        let type = $(this).find('input[name="type"]:checked').val();
        if(!type) { alert('Select a transaction type.'); return; }

        // Confirmation before deposit/withdrawal
        let confirmMessage = type==='deposit' 
            ? 'Are you sure you want to deposit for this client?' 
            : 'Are you sure you want to withdraw for this client?';
        if(!confirm(confirmMessage)) return;

        $.post('../backend/savings_transaction.php', $(this).serialize(), function(resp){
            if(resp?.success){
                closeModal(qs('#addSavingsModal'));
                $('#addSavingsForm')[0].reset();
                $('#clientSearchModal').val(null).trigger('change');
                loadTransactions();
                loadAccounts();
            } else {
                alert(resp?.error || resp?.message || 'Failed to save transaction.');
            }
        }, 'json');

    });

    // ---------- Admin Withdrawal Form ----------
    $('#addAdminWithdrawForm').on('submit', function(e){
        e.preventDefault();
        if(!confirm('Are you sure you want to withdraw the admin fee?')) return;
        $.post('../backend/withdraw_admin_fee.php', $(this).serialize(), function(resp){
            if(resp && resp.status==='success'){
                closeModal(qs('#addAdminWithdrawModal'));
                $('#addAdminWithdrawForm')[0].reset();
                loadAdminWithdrawals();
                $('#sumAdminFee').text(money(resp.pending_admin_fee));
                loadAccounts();
            } else alert(resp?.message || 'Withdrawal failed.');
        },'json');
    });
});

// ---------- Load Transactions ----------
function loadTransactions(q=''){
    $.get('../backend/get_savings.php', {q}, function(rows){
        const body = $('#savingsBody').empty();
        if(!rows?.length){
            body.append('<tr><td colspan="7" style="text-align:center;padding:20px;">No savings transactions found.</td></tr>');
            return;
        }
        rows.forEach(r=>{
            body.append(`<tr>
                <td>${r.client_name||''}</td>
                <td>${r.phone||''}</td>
                <td>${r.date||''}</td>
                <td>${r.type||''}</td>
                <td>${money(r.amount)}</td>
                <td>${money(r.balance)}</td>
                <td>${r.details||''}</td>
            </tr>`);
        });
    },'json');
}

// ---------- Load Accounts ----------
function loadAccounts(q=''){
    $.get('../backend/get_savings_accounts.php', {q}, function(resp){
        const body = $('#accountsBody').empty();
        const accounts = resp.accounts || [];
        const summary = resp.summary || {};

        $('#sumAccounts').text(summary.total_accounts || 0);
        $('#sumAdminFee').text(money(summary.pending_admin_fee));
        $('#sumWithdrawable').text(money(summary.total_withdrawable));
        $('#sumReady').text(summary.ready_to_close || 0);

        if(!accounts.length){
            body.append('<tr><td colspan="10" style="text-align:center;padding:20px;">No savings accounts found.</td></tr>');
            return;
        }

        accounts.forEach(r=>{
            let rowClass = '';
            if(r.status==='Withdrawable') rowClass='withdrawable';
            else if(r.status==='Ready to Close') rowClass='ready-to-close';

            const actionBtn = r.status==='Ready to Close'
                ? `<button class="btn btn-danger btn-close-account" data-id="${r.id}">Close Account</button>`
                : '';

            body.append(`<tr class="${rowClass}">
                <td>${r.full_name}</td>
                <td>${r.telephone}</td>
                <td>${money(r.daily_amount)}</td>
                <td>${r.total_days_paid}</td>
                <td>${money(r.total_paid)}</td>
                <td>${money(r.withdrawable)}</td>
                <td>${money(r.admin_fee)}</td>
                <td>${money(r.already_withdrawn)}</td>
                <td>${r.status}</td>
                <td>${actionBtn}</td>
            </tr>`);
        });

        // Close account click
        $('.btn-close-account').off('click').on('click', function(){
            const id = $(this).data('id');
            if(confirm('Are you sure you want to close this savings account?')){
                $.post('../backend/close_savings.php', {savings_id: id}, function(resp){
                    if(resp && resp.status==='success'){
                        alert(resp.message);
                        loadAccounts();
                        loadTransactions();
                    } else alert(resp?.message || 'Failed to close account.');
                }, 'json');
            }
        });
    },'json');
}

// ---------- Load Admin Withdrawals ----------
function loadAdminWithdrawals(){
    $.get('../backend/get_admin_withdrawals.php', function(resp){
        const body = $('#adminWithdrawalsBody').empty();
        if(resp.status==='success'){
            (resp.withdrawals||[]).forEach(w=>{
                body.append(`<tr>
                    <td>${money(w.amount)}</td>
                    <td>${w.withdrawn_at}</td>
                    <td>${w.note || '-'}</td>
                    <td>${w.admin_name || w.admin_id}</td>
                </tr>`);
            });
        } else {
            body.append('<tr><td colspan="4" style="text-align:center;">No admin withdrawals yet.</td></tr>');
        }
    },'json');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
