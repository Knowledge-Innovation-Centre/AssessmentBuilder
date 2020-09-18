<template>
    <div class="background-grey">
        <flex :items="object.items" :object="object" class="row" direction="column"></flex>
    </div>
</template>

<script>
  import Flex from "./Flex.vue";

  export default {

    name: 'Page',
    components: {
      Flex
    },

    props: {
      object: {
        type: Object,
        required: true,
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
    .background-grey {
        background: #eee;
    }
</style>
