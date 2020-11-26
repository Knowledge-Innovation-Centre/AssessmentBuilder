<template>
  <div class="aoat-flex aoat-flex-col">
    <strong class="aoat-m-1">Label/Question:</strong>
    <input
      v-model="object.label"
      class="aoat-m-1"
      placeholder="Enter question here"
      type="text"
    />
  </div>
</template>

<script>
export default {
  name: "TextareaInput",

  components: {},
  props: {
    object: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      show: false
    };
  },
  methods: {}
};
</script>
<style scoped>
.table > tbody > tr > th,
.table > tbody > tr > td {
  padding: 5px 10px;
}
.table > tbody > tr > th {
  text-align: right;
}
</style>
