<template>
  <div>
    <flex :items="items" direction="column"></flex>
  </div>
</template>

<script>

  export default {
    name: "Column",
    components: {
      Flex: () => import('./Flex.vue'),
    },
    props: {
      object: {
        type: Object,
        required: true
      }
    },
    computed: {
      items() {
        console.log(this.object);
        if (this.object) {
          return this.object.items
        }
        return null
      }
    }
  };
</script>

<style scoped>
</style>
