<template>
  <div>
    {{ object.label }} <template v-if="object.required">*</template><br>
        <label v-for="option in options" :key="option.id">
          <input type="radio" :name="object.key" v-model="value" :value="option.id" />
            {{ option.name }}
        </label>
    </div>
</template>

<script>

  export default {

    name: 'RadioInput',


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
      },
      options() {
        return this.object.options
      }
    },
    methods: {

    }
  };
</script>
