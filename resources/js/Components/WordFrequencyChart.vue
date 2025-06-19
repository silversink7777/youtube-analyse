<template>
  <div class="word-frequency-chart">
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { Chart, registerables } from 'chart.js';

// Chart.jsの全機能を登録
Chart.register(...registerables);

const props = defineProps({
  wordFrequency: {
    type: Array,
    required: true,
    default: () => []
  },
  maxWords: {
    type: Number,
    default: 10
  }
});

const chartCanvas = ref(null);
let chart = null;

const createChart = () => {
  if (!chartCanvas.value || props.wordFrequency.length === 0) return;

  const ctx = chartCanvas.value.getContext('2d');
  
  // 既存のチャートを破棄
  if (chart) {
    chart.destroy();
  }

  // データを準備
  const data = props.wordFrequency.slice(0, props.maxWords);
  const labels = data.map(item => item.word);
  const counts = data.map(item => item.count);
  const percentages = data.map(item => item.percentage);

  // グラデーションカラーを生成
  const colors = data.map((_, index) => {
    const hue = (index * 25) % 360;
    return `hsl(${hue}, 70%, 60%)`;
  });

  chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [
        {
          label: '出現回数',
          data: counts,
          backgroundColor: colors,
          borderColor: colors.map(color => color.replace('60%', '50%')),
          borderWidth: 1,
          borderRadius: 4,
          borderSkipped: false,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const index = context.dataIndex;
              const count = context.parsed.y;
              const percentage = percentages[index];
              return `${context.label}: ${count}回 (${percentage}%)`;
            }
          }
        }
      },
      scales: {
        x: {
          ticks: {
            maxRotation: 45,
            minRotation: 0,
            font: {
              size: 10
            }
          },
          grid: {
            display: false
          }
        },
        y: {
          beginAtZero: true,
          ticks: {
            font: {
              size: 10
            }
          },
          grid: {
            color: 'rgba(0, 0, 0, 0.1)'
          }
        }
      },
      animation: {
        duration: 1000,
        easing: 'easeInOutQuart'
      }
    }
  });
};

// プロパティの変更を監視
watch(() => props.wordFrequency, () => {
  if (props.wordFrequency.length > 0) {
    createChart();
  }
}, { deep: true });

onMounted(() => {
  if (props.wordFrequency.length > 0) {
    createChart();
  }
});

onUnmounted(() => {
  if (chart) {
    chart.destroy();
  }
});
</script>

<style scoped>
.word-frequency-chart {
  width: 100%;
  height: 200px;
  margin: 10px 0;
}
</style> 