<template>
  <div>
    {{ object.label }} <template v-if="object.required">*</template>
    <label class="aoat-w-full" v-for="option in options" :key="option.id">
      <input type="radio"
             :class="hasError ? 'aoat-border-red-400' : ''"
             :name="object.key" v-model="value" :value="option.id" />
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
      },
      hasError: {
        type: Boolean,
        required: false,
        default: false,
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
          return this.$store.dispatch('updateValue', { key: this.object.key, value: newValue, score: score})
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
