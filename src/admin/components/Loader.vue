<template>
  <div class=" aoat-text-center">
    <div v-if="loading" class="lds-roller">
      <div />
      <div />
      <div />
      <div />
      <div />
      <div />
      <div />
      <div />
    </div>
  </div>
</template>

<script>
export default {
  name: "Loader",
  props: {
    loading: {
      type: Boolean,
      required: true
    }
  }
};
</script>
