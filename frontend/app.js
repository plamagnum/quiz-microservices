document.addEventListener('DOMContentLoaded', () => {
    const questionForm = document.getElementById('add-question-form');
    const questionList = document.getElementById('questions-list');
    const questionText = document.getElementById('question-text');

    const apiUrl = '/api/'; // JS взаємодіє ТІЛЬКИ з /api/

    /**
     * Завантажити та відобразити всі запитання
     */
    async function loadQuestions() {
        try {
            const response = await fetch(apiUrl); // GET-запит [26]
            if (!response.ok) throw new Error('Network response was not ok');
            const questions = await response.json();

            questionList.innerHTML = ''; // Очистити список
            questions.forEach(q => {
                const li = document.createElement('li');
                li.textContent = q.text;
                
                const deleteBtn = document.createElement('button');
                deleteBtn.textContent = 'Видалити';
                deleteBtn.className = 'delete-btn';
                deleteBtn.dataset.id = q._id; // Зберігаємо ID для видалення
                
                li.appendChild(deleteBtn);
                questionList.appendChild(li);
            });
        } catch (error) {
            console.error('Failed to load questions:', error);
            questionList.innerHTML = '<li>Не вдалося завантажити запитання.</li>';
        }
    }

    /**
     * Обробник відправки форми (створення запитання)
     */
    questionForm.addEventListener('submit', async (e) => {
        e.preventDefault(); // Запобігти перезавантаженню сторінки

        const data = {
            text: questionText.value,
            // options: 'xxx', // Сюди можна додати варіанти відповідей
        };

        try {
            // POST-запит з JSON-тілом [26, 28]
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) throw new Error('Failed to add question');

            questionText.value = ''; // Очистити поле вводу
            await loadQuestions(); // Оновити список
        } catch (error) {
            console.error('Error adding question:', error);
        }
    });

    /**
     * Обробник кліку на список (для видалення)
     */
    questionList.addEventListener('click', async (e) => {
        // Делегування подій: перевіряємо, чи клікнули саме на кнопку видалення
        if (e.target.classList.contains('delete-btn')) {
            const id = e.target.dataset.id;
            
            if (confirm('Ви впевнені, що хочете видалити це запитання?')) {
                try {
                    // DELETE-запит [29]
                    const response = await fetch(`${apiUrl}?id=${encodeURIComponent(id)}`, {
                        method: 'DELETE'
                    });

                    if (!response.ok) throw new Error('Failed to delete question');
                    
                    await loadQuestions(); // Оновити список
                } catch (error) {
                    console.error('Error deleting question:', error);
                }
            }
        }
    });

    // Початкове завантаження запитань при відкритті сторінки
    loadQuestions();
});