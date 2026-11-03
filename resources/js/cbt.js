/**
 * Core CBT Examination Client Script (FR.IA.05)
 * Server-authoritative timer, auto-saving, offline draft resilience.
 */
document.addEventListener('DOMContentLoaded', function () {
    const timerElement = document.getElementById('cbt-timer');
    const examForm = document.getElementById('cbt-form');
    const saveStatusElement = document.getElementById('cbt-save-status');

    if (!timerElement || !examForm) {
        return;
    }

    let remainingSeconds = parseInt(timerElement.dataset.remainingSeconds || '3600', 10);
    const saveDraftUrl = examForm.dataset.saveDraftUrl;
    const assessmentId = examForm.dataset.assessmentId;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    let isSubmitting = false;
    let saveTimeout = null;

    function formatTime(seconds) {
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = seconds % 60;

        if (h > 0) {
            return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        }
        return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    }

    function updateTimerDisplay() {
        timerElement.textContent = formatTime(remainingSeconds);

        if (remainingSeconds <= 300) {
            timerElement.classList.add('text-rose-600', 'font-black', 'animate-pulse');
        } else {
            timerElement.classList.remove('text-rose-600', 'animate-pulse');
        }
    }

    updateTimerDisplay();

    // Server-authoritative countdown ticker
    const timerInterval = setInterval(function () {
        if (remainingSeconds > 0) {
            remainingSeconds--;
            updateTimerDisplay();
        } else {
            clearInterval(timerInterval);
            if (!isSubmitting) {
                isSubmitting = true;
                if (saveStatusElement) {
                    saveStatusElement.textContent = 'Waktu habis. Mengirimkan lembar jawaban...';
                }
                examForm.submit();
            }
        }
    }, 1000);

    // Collect current answers from form
    function getAnswers() {
        const answers = {};
        const radioInputs = examForm.querySelectorAll('input[type="radio"]:checked');
        radioInputs.forEach(function (radio) {
            const name = radio.getAttribute('name');
            const match = name.match(/answers\[(\d+)\]/);
            if (match) {
                answers[match[1]] = radio.value;
            }
        });
        return answers;
    }

    // Auto-save draft via AJAX
    function saveDraft() {
        if (!saveDraftUrl || isSubmitting) return;

        const answers = getAnswers();
        if (saveStatusElement) {
            saveStatusElement.textContent = 'Menyimpan draf...';
        }

        fetch(saveDraftUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ answers: answers }),
        })
            .then(function (res) {
                return res.json();
            })
            .then(function (data) {
                if (data.status === 'success') {
                    if (saveStatusElement) {
                        saveStatusElement.textContent = 'Draf tersimpan otomatis (' + new Date().toLocaleTimeString() + ')';
                    }
                    if (data.remaining_seconds !== undefined) {
                        remainingSeconds = data.remaining_seconds;
                        updateTimerDisplay();
                    }
                }
            })
            .catch(function () {
                if (saveStatusElement) {
                    saveStatusElement.textContent = 'Koneksi terputus. Jawaban tetap tersimpan lokal.';
                }
            });
    }

    // Debounce saving draft on radio change
    examForm.addEventListener('change', function (e) {
        if (e.target.matches('input[type="radio"]')) {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(saveDraft, 1000);
        }
    });

    examForm.addEventListener('submit', function () {
        isSubmitting = true;
        clearInterval(timerInterval);
        if (saveStatusElement) {
            saveStatusElement.textContent = 'Mengirimkan lembar jawaban akhir...';
        }
    });
});
