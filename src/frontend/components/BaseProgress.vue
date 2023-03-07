<template>
  <div
    :class="[
      { 'aoat-rounded-full': rounded },
      { indeterminate: indeterminate }
    ]"
    class="aoat-w-full aoat-bg-gray-200 aoat-h-2 aoat-relative aoat-overflow-hidden"
  >
    <div
      :aria-valuenow="percentage"
      :class="[
        colour,
        { 'aoat-absolute aoat-top-0': indeterminate },
        { 'aoat-rounded-full': rounded }
      ]"
      :style="{ width: `${percentage}%`, 'background-color': `${colour}` }"
      aria-valuemax="100"
      aria-valuemin="0"
      class="aoat-h-full progressbar"
      role="progressbar"
    >
      <span class="aoat-flex aoat-items-center aoat-h-full">
        <slot />
      </span>
    </div>
  </div>
</template>
<script>
export default {
  inheritAttrs: false,
  props: {
    colour: {
      type: String,
      default: "#008080"
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
