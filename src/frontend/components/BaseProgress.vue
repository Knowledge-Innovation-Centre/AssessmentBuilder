<template>
  <div
      class="aoat-w-full aoat-bg-gray-200 aoat-h-2 aoat-relative aoat-overflow-hidden"
      :class="[{'aoat-rounded-full': rounded}, { indeterminate: indeterminate}]"
  >
    <div
        class="aoat-h-full progressbar"
        :class="[`aoat-bg-${color}-500`, {'aoat-absolute aoat-top-0': indeterminate}, {'aoat-rounded-full': rounded}]"
        role="progressbar"
        :style="{width: `${percentage}%`}"
        :aria-valuenow="percentage"
        aria-valuemin="0"
        aria-valuemax="100"
    >
      <span class="aoat-flex aoat-items-center aoat-h-full">
        <slot></slot>
      </span>
    </div>
  </div>
</template>
<script>
export default {
  inheritAttrs: false,
  props: {
    color: {
      type: String,
      default: "teal"
    },
    percentage: {
      type: Number,
      default: 0
    },
    rounded: {
      type: Boolean,
      default: true
    },
    indeterminate: Boolean
  }
};
</script>
<style scoped>
@keyframes progress-indeterminate {
  0% {
    width: 30%;
    left: -40%;
  }
  60% {
    left: 100%;
    width: 100%;
  }
  to {
    left: 100%;
    width: 0;
  }
}
.progressbar {
  transition: width 0.25s ease;
}
.indeterminate .progressbar {
  animation: progress-indeterminate 1.4s ease infinite;
}
</style>
