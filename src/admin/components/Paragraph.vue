<template>
    <div class="background-grey">
      <textarea class="width-100" v-model="object.value" rows="10"></textarea>
    </div>
</template>

<script>

  export default {

    name: 'Paragraph',
    components: {
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
