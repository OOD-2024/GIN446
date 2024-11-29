<?php

require_once 'includes/dbh.inc.php';


?>
<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .request-list {
            display: grid;
            gap: 16px;
        }

        .request-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 16px;
            align-items: center;
            transition: transform 0.2s;
        }

        .request-card:hover {
            transform: translateY(-2px);
        }

        .request-info h3 {
            margin: 0 0 8px 0;
            color: #2d3748;
        }

        .request-meta {
            color: #718096;
            font-size: 0.875rem;
        }

        .request-status {
            font-weight: 500;
            margin-top: 8px;
        }

        .pending {
            color: #d69e2e;
        }

        .approved {
            color: #38a169;
        }

        .rejected {
            color: #e53e3e;
        }

        .button-group {
            display: flex;
            gap: 8px;
        }

        button {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .approve-btn {
            background-color: #48bb78;
            color: white;
        }

        .approve-btn:hover {
            background-color: #38a169;
        }

        .reject-btn {
            background-color: #f56565;
            color: white;
        }

        .reject-btn:hover {
            background-color: #e53e3e;
        }

        .disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        @media (max-width: 640px) {
            .request-card {
                grid-template-columns: 1fr;
            }

            .button-group {
                justify-content: flex-start;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Request Manager</h1>
            <p>Total Pending: <span id="pending-count">0</span></p>
        </div>

        <div class="request-list" id="requestList">
            <!-- Requests will be inserted here -->
        </div>
    </div>

    <script>
        // Sample request data
        let requests = [
            {
                id: 1,
                title: "Vacation Request",
                requester: "John Doe",
                date: "2024-11-30",
                details: "2 weeks vacation in December",
                status: "pending"
            },
            {
                id: 2,
                title: "Equipment Purchase",
                requester: "Jane Smith",
                date: "2024-11-29",
                details: "New laptop for development team",
                status: "pending"
            },
            {
                id: 3,
                title: "Budget Approval",
                requester: "Mike Johnson",
                date: "2024-11-28",
                details: "Q4 marketing budget increase",
                status: "pending"
            }
        ];

        function createRequestCard(request) {
            return `
                <div class="request-card" id="request-${request.id}">
                    <div class="request-info">
                        <h3>${request.title}</h3>
                        <div class="request-meta">
                            <div>Requested by: ${request.requester}</div>
                            <div>Date: ${request.date}</div>
                            <div>${request.details}</div>
                        </div>
                        <div class="request-status ${request.status}">
                            Status: ${request.status.charAt(0).toUpperCase() + request.status.slice(1)}
                        </div>
                    </div>
                    <div class="button-group">
                        <button 
                            onclick="handleRequest(${request.id}, 'approved')" 
                            class="approve-btn ${request.status !== 'pending' ? 'disabled' : ''}"
                            ${request.status !== 'pending' ? 'disabled' : ''}
                        >
                            Approve
                        </button>
                        <button 
                            onclick="handleRequest(${request.id}, 'rejected')" 
                            class="reject-btn ${request.status !== 'pending' ? 'disabled' : ''}"
                            ${request.status !== 'pending' ? 'disabled' : ''}
                        >
                            Reject
                        </button>
                    </div>
                </div>
            `;
        }

        function updatePendingCount() {
            const pendingCount = requests.filter(r => r.status === 'pending').length;
            document.getElementById('pending-count').textContent = pendingCount;
        }

        function handleRequest(id, newStatus) {
            const request = requests.find(r => r.id === id);
            if (request && request.status === 'pending') {
                request.status = newStatus;

                // Update the UI
                const requestElement = document.getElementById(`request-${id}`);
                requestElement.innerHTML = createRequestCard(request).trim();

                // Update pending count
                updatePendingCount();

                // Show confirmation
                alert(`Request ${id} has been ${newStatus}`);
            }
        }

        function renderRequests() {
            const requestList = document.getElementById('requestList');
            requestList.innerHTML = requests.map(createRequestCard).join('');
            updatePendingCount();
        }

        // Initial render
        renderRequests();
    </script>
</body>

</html>