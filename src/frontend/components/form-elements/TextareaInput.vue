<template>
  <div>
      <label>
          {{ object.label }} <template v-if="object.required">*</template>
          <textarea name="label" :placeholder="object.placeholder" v-model="value" />
      </label>

    </div>
</template>

<script>
  export default {

    name: 'TextareaInput',

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

    computed: {
      value: {
        get () {
          return this.$store.state.assessment[this.object.key]
        },
        set (newValue) {
          return this.$store.dispatch('updateValue', { key: this.object.key, value: newValue})
        }
      }
    },
    methods: {
    }
  };
</script>
