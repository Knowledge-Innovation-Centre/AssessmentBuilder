<template>
  <div>
    <p v-html="object.label" />
  </div>
</template>

<script>
export default {
  name: "Paragraph",
  components: {},

  props: {
    object: {
      type: Object,
      required: true
    }
  },

  data() {
    return {};
  },
  methods: {}
};
</script>
