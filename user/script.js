const exercises = [
  {
    id: 1,
    title: "Treadmill Walk",
    level: "novice",
    type: "cardio",
    duration: "10 min × 1 Set",
    equipment: "Treadmill",
    videoId: "aUaInS6HIGo",
    image: "https://img.youtube.com/vi/aUaInS6HIGo/hqdefault.jpg",
    image__musles: "https://fitnessprogramer.com/wp-content/uploads/2021/06/what-muscles-does-the-treadmill-work.png",
    progress: 75,
    calories: 85,
  },
  {
    id: 2,
    title: "Dumbbell Rows",
    level: "novice",
    type: "strength",
    duration: "4-8 reps × 3 Sets",
    equipment: "Dumbbells",
    videoId: "pYcpY20QaE8",
    image: "https://img.youtube.com/vi/pYcpY20QaE8/hqdefault.jpg",
    image__musles: "https://fitnessprogramer.com/wp-content/uploads/2021/05/SEATED-ROW-MACHINE-muscle-worked-300x300.png",
    progress: 60,
    calories: 120,
  },
  {
    id: 3,
    title: "Squats",
    level: "intermediate",
    type: "strength",
    duration: "12-15 reps × 3 Sets",
    equipment: "Bodyweight",
    videoId: "YaXPRqUwItQ",
    image: "https://img.youtube.com/vi/YaXPRqUwItQ/hqdefault.jpg",
    image__musles: "https://fitnessprogramer.com/wp-content/uploads/2021/02/squat-muscle-worked-300x300.png",
    progress: 90,
    calories: 150,
  },
  {
    id: 4,
    title: "Yoga",
    level: "novice",
    type: "flexibility",
    duration: "15 min × 1 Set",
    equipment: "Yoga Mat",
    videoId: "v7AYKMP6rOE",
    image: "https://img.youtube.com/vi/v7AYKMP6rOE/hqdefault.jpg",
    image__musles: "https://i.pinimg.com/236x/da/9d/c0/da9dc0b57a0ebf761418880f379824c5.jpg",
    progress: 30,
    calories: 75,
  },
  {
    id: 5,
    title: "Cardio Dance",
    level: "intermediate",
    type: "cardio",
    duration: "20 min × 1 Set",
    equipment: "None",
    videoId: "gC_L9qAHVJ8",
    image: "https://img.youtube.com/vi/gC_L9qAHVJ8/hqdefault.jpg",
    image__musles: "https://fitnessprogramer.com/wp-content/uploads/2021/02/squat-muscle-worked-300x300.png",
    progress: 45,
    calories: 200,
  },
  {
    id: 6,
    title: "Push-ups",
    level: "expert",
    type: "strength",
    duration: "8-12 reps × 4 Sets",
    equipment: "Wall/Floor",
    videoId: "IODxDxX7oi4",
    image: "https://img.youtube.com/vi/IODxDxX7oi4/hqdefault.jpg",
    image__musles: "https://fitnessprogramer.com/wp-content/uploads/2021/02/diamond-push-up-muscle-worked-300x300.png",
    progress: 20,
    calories: 110,
  },
  {
    id: 7,
    title: "Planks",
    level: "intermediate",
    type: "strength",
    duration: "30-60 sec × 3 Sets",
    equipment: "Mat",
    videoId: "pSHjTRCQxIw",
    image: "https://img.youtube.com/vi/pSHjTRCQxIw/hqdefault.jpg",
    image__musles: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQmMzVFszyPb78E2AMUh7AKp3jgXWWXsz6LYQ&s",
    progress: 80,
    calories: 95,
  },
  {
    id: 8,
    title: "Stretches",
    level: "novice",
    type: "flexibility",
    duration: "10 min × 1 Set",
    equipment: "Mat",
    videoId: "L_xrDAtykMI",
    image: "https://img.youtube.com/vi/L_xrDAtykMI/hqdefault.jpg",
    image__musles: "https://fitnessprogramer.com/wp-content/uploads/2021/05/Hyperextension-muscle-worked-300x300.png",
    progress: 100,
    calories: 40,
  },
];

let currentFilter = "all";
let timerInterval;
let timerSeconds = 0;
let isTimerRunning = false;

// Initialize the app
document.addEventListener("DOMContentLoaded", function () {
  createMagicalBackground();
  renderExercises();
  setupEventListeners();
  startFloatingHearts();
  updateStats();
});

function createMagicalBackground() {
  const bg = document.getElementById("magicalBg");
  for (let i = 0; i < 50; i++) {
    const sparkle = document.createElement("div");
    sparkle.className = "sparkle";
    sparkle.style.left = Math.random() * 100 + "%";
    sparkle.style.top = Math.random() * 100 + "%";
    sparkle.style.animationDelay = Math.random() * 3 + "s";
    bg.appendChild(sparkle);
  }
}

function startFloatingHearts() {
  const container = document.getElementById("floatingHearts");
  setInterval(() => {
    const heart = document.createElement("div");
    heart.className = "heart";
    heart.innerHTML = "💖";
    heart.style.left = Math.random() * 100 + "%";
    heart.style.animationDelay = Math.random() * 2 + "s";
    container.appendChild(heart);

    setTimeout(() => {
      if (heart.parentNode) {
        heart.parentNode.removeChild(heart);
      }
    }, 4000);
  }, 3000);
}

function renderExercises() {
  const grid = document.getElementById("exerciseGrid");
  grid.innerHTML = "";

  const filteredExercises = exercises.filter((exercise) => {
    if (currentFilter === "all") return true;
    return exercise.type === currentFilter || exercise.level === currentFilter;
  });

  filteredExercises.forEach((exercise, index) => {
    const card = createExerciseCard(exercise);
    card.style.animationDelay = index * 0.1 + "s";
    grid.appendChild(card);
  });
}

function createExerciseCard(exercise) {
  const card = document.createElement("div");
  card.className = "exercise-card";
  card.innerHTML = `
    <div class="card-header" onclick="openVideo('${exercise.videoId}', '${
    exercise.title
  }')">
      <img class="video-thumb" src="${exercise.image}" alt="${exercise.title}">
      <div class="play-overlay">
        <div class="play-icon"></div>
      </div>
    </div>
    <div class="card-body">
      <h3 class="exercise-title">${exercise.title}</h3>
      <div class="exercise-details">
        <div class="detail-item">
          <div class="detail-icon level-${exercise.level}"></div>
          <span>${
            exercise.level.charAt(0).toUpperCase() + exercise.level.slice(1)
          }</span>
        </div>
        <div class="detail-item">
          <div class="detail-icon" style="background: #3498db;"></div>
          <span>${
            exercise.type.charAt(0).toUpperCase() + exercise.type.slice(1)
          }</span>
        </div>
        <div class="detail-item">
          <div class="detail-icon" style="background: #9b59b6;"></div>
          <span>${exercise.duration}</span>
        </div>
        <div class="detail-item">
          <div class="detail-icon" style="background: #e67e22;"></div>
          <span>${exercise.calories} cal</span>
        </div>
      </div>

      <div class="muscle-image-wrapper">
        <img src="${
          exercise.image__musles
        }" alt="Muscle Target" class="muscle-large" />
      </div>

      <div class="progress-section">
        <div class="progress-bar">
          <div class="progress-fill" style="width: ${exercise.progress}%"></div>
        </div>
        <small style="color: #666;">${exercise.progress}% Complete</small>
      </div>

      <div class="action-buttons">
        <button class="btn btn-primary" onclick="markComplete(${exercise.id})">
          ${exercise.progress === 100 ? "✅ Completed" : "▶️ Start"}
        </button>
        <button class="btn btn-secondary" onclick="showTimer()">⏱️ Timer</button>
      </div>
    </div>
  `;
  return card;
}

function setupEventListeners() {
  const filterTabs = document.querySelectorAll(".filter-tab");
  filterTabs.forEach((tab) => {
    tab.addEventListener("click", function () {
      filterTabs.forEach((t) => t.classList.remove("active"));
      this.classList.add("active");
      currentFilter = this.dataset.filter;
      renderExercises();
    });
  });
}

function openVideo(videoId, title) {
  const popup = document.getElementById("videoPopup");
  const frame = document.getElementById("videoFrame");
  const titleEl = document.getElementById("videoTitle");

  frame.src = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
  titleEl.textContent = title;
  popup.style.display = "flex";
}

function closeVideo() {
  const popup = document.getElementById("videoPopup");
  const frame = document.getElementById("videoFrame");

  frame.src = "";
  popup.style.display = "none";
}

function markComplete(exerciseId) {
  const exercise = exercises.find((ex) => ex.id === exerciseId);
  if (exercise) {
    exercise.progress = 100;
    renderExercises();
    updateStats();
    celebrateCompletion();
  }
}

function celebrateCompletion() {
  // Create celebration effect
  for (let i = 0; i < 20; i++) {
    setTimeout(() => {
      const celebration = document.createElement("div");
      celebration.innerHTML = "🎉";
      celebration.style.position = "fixed";
      celebration.style.left = Math.random() * 100 + "%";
      celebration.style.top = Math.random() * 100 + "%";
      celebration.style.fontSize = "30px";
      celebration.style.pointerEvents = "none";
      celebration.style.animation = "floatUp 2s ease-out forwards";
      celebration.style.zIndex = "10000";
      document.body.appendChild(celebration);

      setTimeout(() => {
        if (celebration.parentNode) {
          celebration.parentNode.removeChild(celebration);
        }
      }, 2000);
    }, i * 100);
  }
}

function showTimer() {
  const timer = document.getElementById("timerWidget");
  timer.classList.add("active");
}

function startTimer() {
  if (!isTimerRunning) {
    isTimerRunning = true;
    timerInterval = setInterval(() => {
      timerSeconds++;
      updateTimerDisplay();
    }, 1000);
  }
}

function pauseTimer() {
  isTimerRunning = false;
  clearInterval(timerInterval);
}

function resetTimer() {
  isTimerRunning = false;
  clearInterval(timerInterval);
  timerSeconds = 0;
  updateTimerDisplay();
}

function updateTimerDisplay() {
  const minutes = Math.floor(timerSeconds / 60);
  const seconds = timerSeconds % 60;
  const display = document.getElementById("timerDisplay");
  display.textContent = `${minutes.toString().padStart(2, "0")}:${seconds
    .toString()
    .padStart(2, "0")}`;
}

function updateStats() {
  const completed = exercises.filter((ex) => ex.progress === 100).length;
  document.getElementById("completedToday").textContent = completed;

  // Update other stats dynamically
  const totalCalories = exercises.reduce(
    (sum, ex) => sum + (ex.progress === 100 ? ex.calories : 0),
    0
  );
  document.getElementById("totalTime").textContent = Math.round(
    totalCalories / 10
  ); // Rough estimate
}

// Close video on escape key
document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") {
    closeVideo();
  }
});

// Close video on background click
document.getElementById("videoPopup").addEventListener("click", function (e) {
  if (e.target === this) {
    closeVideo();
  }
});

const style = document.createElement("style");
style.innerHTML = `
  .muscle-thumb {
    width: 28px;
    height: 28px;
    object-fit: cover;
    border-radius: 5px;
    margin-left: 6px;
    border: 1px solid #ddd;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    transition: transform 0.3s ease;
  }
  .muscle-thumb:hover {
    transform: scale(1.4);
    z-index: 10;
  }
`;
document.head.appendChild(style);
