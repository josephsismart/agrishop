<?php
if (!$this->session->agrishop_login_level) return;
$uri       = $this->session->agrishop_login_uri;
$person_id = $this->session->agrishop_person_id;
?>

<!-- ============================================================
     NOTIFICATION SYSTEM — global/Notifications.php
     Fixed: duplicate audio, cleaner UI, proper count badge,
            correct Bootstrap 4 classes, outside-click close.
============================================================ -->

<!-- Notification dropdown panel -->
<div id="notifDropdown"
    style="position:fixed;bottom:76px;right:20px;z-index:9999;
           display:none;width:320px;max-height:420px;overflow-y:auto;
           background:#fff;border-radius:10px;
           box-shadow:0 4px 24px rgba(0,0,0,.18);border:1px solid #dee2e6;">
    <div style="padding:10px 14px;border-bottom:1px solid #dee2e6;
                display:flex;justify-content:space-between;align-items:center;
                position:sticky;top:0;background:#fff;z-index:1;">
        <strong style="font-size:14px;">&#128276; Notifications</strong>
        <button onclick="markAllRead()" class="btn btn-sm btn-outline-secondary"
            style="font-size:11px;padding:2px 8px;">
            Mark all read
        </button>
    </div>
    <div id="notifList" style="padding:6px 0;"></div>
</div>

<!-- Floating bell button -->
<button id="notifBell" onclick="toggleNotifDropdown()"
    style="position:fixed;bottom:20px;right:20px;z-index:9999;
           width:50px;height:50px;border-radius:50%;
           background:#e67e22;border:none;color:#fff;font-size:20px;
           box-shadow:0 3px 14px rgba(0,0,0,.22);cursor:pointer;
           display:flex;align-items:center;justify-content:center;">
    <i class="fa fa-bell"></i>
    <span id="notifCount"
        style="position:absolute;top:-4px;right:-4px;
               background:#dc3545;color:#fff;border-radius:50%;
               font-size:10px;width:18px;height:18px;
               display:none;align-items:center;justify-content:center;
               font-weight:bold;">0</span>
</button>

<style>
@keyframes bellShake {
    0%,100% { transform:rotate(0);  }
    20%      { transform:rotate(-15deg); }
    40%      { transform:rotate(15deg);  }
    60%      { transform:rotate(-10deg); }
    80%      { transform:rotate(10deg);  }
}
#notifBell.ringing { animation: bellShake .5s ease; }
</style>

<script>
(function () {
    /* ── Audio (single instance, shared with page audio if exists) ── */
    if (!window._notifAudio) {
        window._notifAudio = new Audio("<?= base_url('dist/notification/notify.wav') ?>");
        window._notifAudio.volume = 1.0;
    }
    if (typeof window._audioUnlocked === 'undefined') window._audioUnlocked = false;

    // Unlock audio on first user interaction
    document.addEventListener('click', function unlockNotifAudio() {
        window._notifAudio.play()
            .then(function() {
                window._notifAudio.pause();
                window._notifAudio.currentTime = 0;
                window._audioUnlocked = true;
            })
            .catch(function() {});
        document.removeEventListener('click', unlockNotifAudio);
    }, { once: true });

    /* ── State ── */
    var _lastCount  = 0;
    var _isOpen     = false;

    /* ── Sound ── */
    function playNotifSound() {
        if (!window._audioUnlocked) return;
        var a = window._notifAudio;
        if (!a.paused) { a.pause(); a.currentTime = 0; }
        a.play().catch(function(){});
    }

    /* ── Fetch count every 5 s ── */
    function fetchNotifications() {
        $.get("<?= base_url('getNotificationCount') ?>", function(res) {
            try {
                var d     = JSON.parse(res);
                var count = parseInt(d.count) || 0;
                var badge = document.getElementById('notifCount');
                var bell  = document.getElementById('notifBell');

                if (count > 0) {
                    badge.style.display = 'flex';
                    badge.textContent   = count > 99 ? '99+' : count;
                } else {
                    badge.style.display = 'none';
                }

                // Play sound + toast only on NEW notifications
                if (count > _lastCount && _lastCount >= 0) {
                    playNotifSound();
                    if (typeof toastr !== 'undefined') {
                        toastr.info('You have ' + (count - _lastCount) + ' new notification(s)!');
                    }
                    // Bell ring animation
                    bell.classList.add('ringing');
                    setTimeout(function() { bell.classList.remove('ringing'); }, 600);
                }
                _lastCount = count;
            } catch(e) {}
        }).fail(function() {/* silent */});
    }

    /* ── Load notification list ── */
    function loadNotifList() {
        var list = document.getElementById('notifList');
        list.innerHTML = '<div style="text-align:center;padding:16px;"><i class="fa fa-spinner fa-spin text-muted"></i></div>';

        $.get("<?= base_url('getUnreadNotifications') ?>", function(res) {
            try {
                var items = JSON.parse(res);
                if (!items.length) {
                    list.innerHTML = '<div style="text-align:center;color:#aaa;padding:24px;font-size:13px;">No new notifications</div>';
                    return;
                }
                var iconMap = { SUCCESS:'✅', DANGER:'❌', INFO:'🔔', WARNING:'⚠️' };
                var bgMap   = { SUCCESS:'#d4edda', DANGER:'#f8d7da', INFO:'#d1ecf1', WARNING:'fff3cd' };

                list.innerHTML = items.map(function(n) {
                    var icon = iconMap[n.type] || '🔔';
                    var bg   = bgMap[n.type]   || '#f8f9fa';
                    return '<div style="padding:10px 14px;border-bottom:1px solid #f0f0f0;background:' + bg + ';margin:2px 6px;border-radius:8px;">' +
                        '<div style="font-size:13px;font-weight:600;">' + icon + ' ' + n.title + '</div>' +
                        '<div style="font-size:12px;color:#555;margin-top:2px;">' + n.message + '</div>' +
                        '<div style="font-size:10px;color:#aaa;margin-top:4px;">' + n.time_ago + '</div>' +
                        '</div>';
                }).join('');
            } catch(e) {
                list.innerHTML = '<div style="text-align:center;color:#aaa;padding:24px;font-size:13px;">Could not load notifications.</div>';
            }
        });
    }

    /* ── Toggle ── */
    window.toggleNotifDropdown = function() {
        var dropdown = document.getElementById('notifDropdown');
        _isOpen = !_isOpen;
        dropdown.style.display = _isOpen ? 'block' : 'none';
        if (_isOpen) loadNotifList();
    };

    /* ── Mark all read ── */
    window.markAllRead = function() {
        $.post("<?= base_url('markNotificationsRead') ?>", function() {
            _lastCount = 0;
            document.getElementById('notifCount').style.display = 'none';
            document.getElementById('notifList').innerHTML =
                '<div style="text-align:center;color:#aaa;padding:24px;font-size:13px;">No new notifications</div>';
            document.getElementById('notifDropdown').style.display = 'none';
            _isOpen = false;
        });
    };

    /* ── Close on outside click ── */
    document.addEventListener('click', function(e) {
        var bell     = document.getElementById('notifBell');
        var dropdown = document.getElementById('notifDropdown');
        if (bell && dropdown && !bell.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
            _isOpen = false;
        }
    });

    /* ── Start polling ── */
    fetchNotifications();
    setInterval(fetchNotifications, 5000);
})();
</script>
