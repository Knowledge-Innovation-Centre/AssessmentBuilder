<template>
  <div>
    <flex :items="items" :object="object" direction="row"></flex>
  </div>
</template>

<script>
  import Flex from "./Flex.vue";

  export default {
    name: "Column",
    components: {
      Flex,
    },
    props: {
      object: {
        type: Object,
        required: true
      }
    },
    computed: {
      items() {
        if (this.object) {
          return this.object.items
        }
        return null
      }
    },
    data () {
      return {
      };
    },
    methods: {
    }
  };
</script>

<style scoped>
</style>
