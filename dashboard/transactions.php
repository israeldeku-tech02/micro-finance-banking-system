<?php 
session_start();
if (empty($_SESSION['user_id'])) { header('Location: ../index.php'); exit; }

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../includes/footer.php';
?>

<div class="content">
    <h2>Normal Savings Management</h2>

    <!-- Top Controls -->
    <div class="top-controls">
        <input type="text" id="searchTransactions" placeholder="Search by client name or phone...">
        <div class="right-controls">
            <div class="view-toggle">
                <button id="tabTransactions" class="btn btn-tab active">Transactions</button>
                <button id="tabAccounts" class="btn btn-tab">Accounts Summary</button>
                <button id="tabAdminWithdrawals" class="btn btn-tab">Admin Withdrawals</button>
            </div>
            <button id="addTransactionBtn" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Transaction
            </button>
            <button id="addAdminWithdrawBtn" class="btn btn-success" style="display:none;">
                <i class="fas fa-university"></i> Withdraw Admin Fee
            </button>
        </div>
    </div>

    <!-- Transactions Section -->
    <div id="transactionsSection">
        <table class="table" id="transactionsTable">
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
            <tbody id="transactionsBody"></tbody>
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
                    <th>Days Paid</th>
                    <th>Clients Total Paid</th>
                    <th>Clients Amount Withdrawable</th>
                    <th>Monthly Charges</th>
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

<!-- Add Transaction Modal -->
<div id="addTransactionModal" class="modal">
    <div class="modal-content" style="max-width:650px;">
        <span class="close">&times;</span>
        <h3>Add Transaction</h3>
        <form id="addTransactionForm">
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

<!-- Admin Withdraw Modal -->
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

<!-- Custom Confirm Modal -->
<div id="confirmModal" class="modal">
    <div class="modal-content" style="max-width:400px;">
        <span class="close">&times;</span>
        <h3>Confirm Action</h3>
        <p id="confirmModalMessage">Are you sure?</p>
        <div class="form-actions" style="text-align:right; margin-top:20px;">
            <button id="confirmCancelBtn" class="btn btn-secondary">Cancel</button>
            <button id="confirmOkBtn" class="btn btn-success">Confirm</button>
        </div>
    </div>
</div>

<style>

/*=====================================================
    TABLE
=====================================================*/

.table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
    border-radius:16px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(15,23,42,.05);
    font-size:14px;
}

.table th{

    background:#f8fafc;

    color:#475569;

    text-transform:uppercase;

    letter-spacing:.4px;

    font-size:13px;

    padding:16px;

    font-weight:700;

    border-bottom:1px solid #e2e8f0;
}

.table td{

    padding:15px;

    border-bottom:1px solid #edf2f7;
}

.table tbody tr{

    transition:.2s;
}

.table tbody tr:hover{

    background:#f8fbff;
}

/*=====================================================
    BUTTONS
=====================================================*/

.btn{

    padding:11px 18px;

    border:none;

    border-radius:12px;

    cursor:pointer;

    font-size:14px;

    font-weight:600;

    transition:.25s;
}

.btn:hover{

    transform:translateY(-2px);

    box-shadow:0 10px 20px rgba(0,0,0,.12);
}

.btn-primary{

    background:linear-gradient(135deg,#2563eb,#1d4ed8);

    color:white;
}

.btn-success{

    background:linear-gradient(135deg,#16a34a,#15803d);

    color:white;
}

.btn-secondary{

    background:#64748b;

    color:white;
}

/*=====================================================
    TOP CONTROLS
=====================================================*/

.top-controls{

    display:flex;

    justify-content:space-between;

    align-items:center;

    gap:18px;

    margin-bottom:25px;
}

.top-controls input{

    width:380px;

    padding:12px 16px;

    border:1px solid #dbe3ee;

    border-radius:12px;

    background:#fff;

    transition:.25s;

    font-size:14px;
}

.top-controls input:focus{

    outline:none;

    border-color:#2563eb;

    box-shadow:0 0 0 4px rgba(37,99,235,.12);
}

/*=====================================================
    SUMMARY CARDS
=====================================================*/

.summary-cards{

    display:grid;

    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));

    gap:18px;

    margin-bottom:25px;
}

.summary-cards .card{

    position:relative;

    overflow:hidden;

    background:linear-gradient(145deg,#ffffff,#f8fbff);

    border:1px solid #e2e8f0;

    border-radius:18px;

    padding:22px;

    box-shadow:0 12px 30px rgba(15,23,42,.06);

    transition:.3s;
}

.summary-cards .card:hover{

    transform:translateY(-4px);

    box-shadow:0 18px 35px rgba(37,99,235,.12);
}

.summary-cards .card::before{

    content:"";

    position:absolute;

    top:0;

    left:0;

    width:5px;

    height:100%;

    background:#2563eb;
}

.card-title{

    font-size:12px;

    color:#64748b;

    text-transform:uppercase;

    letter-spacing:.5px;

    margin-bottom:10px;
}

.card-value{

    font-size:28px;

    font-weight:700;

    color:#1e293b;
}

/*=====================================================
    MODAL
=====================================================*/

.modal{

    display:none;

    position:fixed;

    inset:0;

    background:rgba(15,23,42,.45);

    backdrop-filter:blur(6px);

    z-index:9999;

    animation:fadeIn .25s ease;
}
.modal-content::before{
    content:"";
    display:block;
    height:6px;
    margin:-30px -30px 24px;
    border-radius:22px 22px 0 0;
    background:linear-gradient(
        90deg,
        #2563eb,
        #3b82f6,
        #60a5fa
    );
}
.modal-content{

    position:relative;

    width:min(900px,92vw);

    margin:35px auto;

    max-height:92vh;

    overflow-y:auto;

    background:white;

    border-radius:22px;

    padding:30px;

    box-shadow:

        0 30px 70px rgba(15,23,42,.18),

        0 10px 25px rgba(15,23,42,.08);

    animation:pop .3s ease;
}

.modal-content h2,
.modal-content h3{

    margin:0;

    padding-bottom:18px;

    border-bottom:1px solid #edf2f7;

    color:#1e3a8a;

    font-size:28px;
}

.modal .close{

    position:absolute;

    right:22px;

    top:20px;

    width:40px;

    height:40px;

    display:flex;

    justify-content:center;

    align-items:center;

    border-radius:50%;

    cursor:pointer;

    color:#64748b;

    transition:.25s;
}

.modal .close:hover{

    background:#eff6ff;

    color:#2563eb;

    transform:rotate(90deg);
}

/*=====================================================
    FORM
=====================================================*/

.form-grid{

    display:grid;

    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));

    gap:20px;

    margin-top:25px;
}

.form-grid .full{

    grid-column:1/-1;
}

.field{

    display:flex;

    flex-direction:column;
}

.field label{

    margin-bottom:8px;

    font-size:13px;

    font-weight:600;

    color:#475569;
}

.field input,
.field textarea,
.field select{

    width:100%;

    padding:13px 15px;

    border:1px solid #dbe3ee;

    border-radius:12px;

    background:#fcfdff;

    font-size:14px;

    transition:.25s;
}

.field textarea{

    min-height:110px;

    resize:vertical;
}

.field input:hover,
.field textarea:hover,
.field select:hover{

    border-color:#93c5fd;
}

.field input:focus,
.field textarea:focus,
.field select:focus{

    outline:none;

    border-color:#2563eb;

    background:white;

    box-shadow:0 0 0 4px rgba(37,99,235,.12);
}

/*=====================================================
    RADIO
=====================================================*/

.radio-row{

    display:flex;

    align-items:center;

    gap:20px;

    padding:12px;

    background:#f8fafc;

    border:1px solid #e2e8f0;

    border-radius:12px;
}

/*=====================================================
    FORM FOOTER
=====================================================*/

.form-actions{

    margin-top:28px;

    padding-top:20px;

    border-top:1px solid #e5e7eb;

    display:flex;

    justify-content:flex-end;

    gap:12px;
}

/*=====================================================
    SCROLLBAR
=====================================================*/

.modal-content::-webkit-scrollbar{

    width:10px;
}

.modal-content::-webkit-scrollbar-track{

    background:#f1f5f9;
}

.modal-content::-webkit-scrollbar-thumb{

    background:#94a3b8;

    border-radius:20px;
}

.modal-content::-webkit-scrollbar-thumb:hover{

    background:#64748b;
}

/*=====================================================
    ANIMATION
=====================================================*/

@keyframes pop{

    from{

        opacity:0;

        transform:translateY(25px) scale(.97);
    }

    to{

        opacity:1;

        transform:translateY(0) scale(1);
    }
}

@keyframes fadeIn{

    from{

        opacity:0;
    }

    to{

        opacity:1;
    }
}

/*=====================================================
    RESPONSIVE
=====================================================*/

@media(max-width:900px){

    .top-controls{

        flex-direction:column;

        align-items:stretch;
    }

    .top-controls input{

        width:100%;
    }

    .form-grid{

        grid-template-columns:1fr;
    }

    .modal-content{

        width:95vw;

        padding:22px;
    }
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

// Custom confirm modal
const confirmModal = qs('#confirmModal');
const confirmMessage = qs('#confirmModalMessage');
const confirmOkBtn = qs('#confirmOkBtn');
const confirmCancelBtn = qs('#confirmCancelBtn');
function showConfirm(message, callback){
    confirmMessage.textContent = message;
    openModal(confirmModal);

    const okHandler = () => { 
        closeModal(confirmModal); 
        confirmOkBtn.removeEventListener('click', okHandler);
        confirmCancelBtn.removeEventListener('click', cancelHandler);
        callback(true); 
    };

    const cancelHandler = () => { 
        closeModal(confirmModal); 
        confirmOkBtn.removeEventListener('click', okHandler);
        confirmCancelBtn.removeEventListener('click', cancelHandler);
        callback(false); 
    };

    confirmOkBtn.addEventListener('click', okHandler);
    confirmCancelBtn.addEventListener('click', cancelHandler);
}

// Tabs
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
    if(which==='transactions') loadTransactions(qs('#searchTransactions').value||'');
    if(which==='accounts') loadAccounts(qs('#searchTransactions').value||'');
    if(which==='admin'){
        loadAdminWithdrawals();
        qs('#addAdminWithdrawBtn').style.display='inline-block';
    } else qs('#addAdminWithdrawBtn').style.display='none';
}

qsa('.modal .close').forEach(x=>x.addEventListener('click',()=>closeModal(x.closest('.modal'))));
window.addEventListener('click', e=>{ qsa('.modal').forEach(m=>{ if(e.target===m) closeModal(m); }); });

qs('#addTransactionBtn').addEventListener('click', ()=>openModal(qs('#addTransactionModal')));
qs('#addAdminWithdrawBtn').addEventListener('click', ()=>openModal(qs('#addAdminWithdrawModal')));

qs('#tabTransactions').addEventListener('click', ()=>activateTab('transactions'));
qs('#tabAccounts').addEventListener('click', ()=>activateTab('accounts'));
qs('#tabAdminWithdrawals').addEventListener('click', ()=>activateTab('admin'));

$(function(){
    $('#clientSearchModal').select2({
        dropdownParent: $('#addTransactionModal'),
        placeholder:'Search/select client...',
        width:'100%',
        allowClear:true,
        ajax:{url:'../backend/search_clients.php', dataType:'json', delay:250, data:params=>({q:params.term}), processResults:data=>({results:data})}
    });

    loadTransactions();
    loadAccounts();

    $('#searchTransactions').on('input', function(){
        if(qs('#tabTransactions').classList.contains('active')) loadTransactions(this.value);
        else if(qs('#tabAccounts').classList.contains('active')) loadAccounts(this.value);
    });

    // Add Transaction Form
    $('#addTransactionForm').on('submit', function(e){
        e.preventDefault();
        let type = $(this).find('input[name="type"]:checked').val();
        let message = type==='deposit' ? 'Are you sure you want to deposit for this client?' : 'Are you sure you want to withdraw for this client?';
        showConfirm(message, confirmed=>{
            if(!confirmed) return;
            $.post('../backend/add_transaction.php', $(this).serialize(), function(resp){
                if(resp && resp.success){
                    closeModal(qs('#addTransactionModal'));
                    $('#addTransactionForm')[0].reset();
                    $('#clientSearchModal').val(null).trigger('change');
                    loadTransactions();
                    loadAccounts();
                } else alert(resp?.error || 'Failed to save transaction.');
            }, 'json');
        });
    });

    // Admin Withdraw Form
    $('#addAdminWithdrawForm').on('submit', function(e){
        e.preventDefault();
        showConfirm('Are you sure you want to withdraw this admin fee?', confirmed=>{
            if(!confirmed) return;
            $.post('../backend/withdraw_admin_fee.php', $(this).serialize(), function(resp){
                if(resp && resp.success){
                    closeModal(qs('#addAdminWithdrawModal'));
                    $('#addAdminWithdrawForm')[0].reset();
                    loadAdminWithdrawals();
                    $('#sumAdminFee').text(money(resp.pending_admin_fee));
                    loadAccounts();
                } else alert(resp?.error || resp?.message || 'Withdrawal failed.');
            }, 'json');
        });
    });
});

// Transactions
function loadTransactions(q=''){
    $.get('../backend/get_transactions.php', {q}, function(resp){
        const rows = resp.transactions || [];
        const body = $('#transactionsBody').empty();
        if(!rows.length){
            body.append('<tr><td colspan="7" style="text-align:center;padding:20px;">No transactions found.</td></tr>');
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
    }, 'json');
}

// Accounts
function loadAccounts(q=''){
    $.get('../backend/get_transactions.php', {q}, function(resp){
        const body = $('#accountsBody').empty();
        const accounts = resp.accounts || [];
        const summary = resp.summary || {};

        $('#sumAccounts').text(summary.total_accounts || 0);
        $('#sumAdminFee').text(money(summary.pending_admin_fee));
        $('#sumWithdrawable').text(money(summary.total_withdrawable));
        $('#sumReady').text(summary.ready_to_close || 0);

        if(!accounts.length){
            body.append('<tr><td colspan="9" style="text-align:center;padding:20px;">No accounts found.</td></tr>');
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
                <td>${r.total_days_paid}</td>
                <td>${money(r.total_paid)}</td>
                <td>${money(r.withdrawable)}</td>
                <td>${money(r.monthly_charges)}</td>
                <td>${money(r.already_withdrawn)}</td>
                <td>${r.status}</td>
                <td>${actionBtn}</td>
            </tr>`);
        });

        // Close account with modal confirmation
        $('.btn-close-account').off('click').on('click', function(){
            const id = $(this).data('id');
            showConfirm('Are you sure you want to close this account?', confirmed=>{
                if(!confirmed) return;
                $.post('../backend/close_account.php', {account_id: id}, function(resp){
                    if(resp && resp.status==='success'){
                        alert(resp.message);
                        loadAccounts();
                        loadTransactions();
                    } else alert(resp?.message || 'Failed to close account.');
                }, 'json');
            });
        });
    },'json');
}

// Admin Withdrawals
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
