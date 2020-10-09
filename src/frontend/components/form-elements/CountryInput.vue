<template>
  <div>
        <label>
            {{ object.label }} <template v-if="object.required">*</template><br>
            <select v-model="value">
              <option :value="null" disabled hidden>{{ object.placeholder }}</option>
                <option v-for="option in options" :key="option.id" :value="option.id">{{ option.name }}</option>
            </select>
        </label>
    </div>
</template>

<script>

  export default {

    name: 'CountryInput',


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
