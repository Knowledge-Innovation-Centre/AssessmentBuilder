<template>
    <div>
      <p>{{ object.value }}</p>
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
