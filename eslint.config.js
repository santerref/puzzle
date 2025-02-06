// import { FlatESLintConfig } from '@eslint/eslintrc';
import vueParser from 'vue-eslint-parser';
import pluginVue from 'eslint-plugin-vue'
import parserTs from '@typescript-eslint/parser'
import tseslint from 'typescript-eslint'
import stylisticTs from '@stylistic/eslint-plugin-ts'

export default tseslint.config(
    {
        ignores: [
            '*',
            '!core',
        ],
    },
    ...tseslint.configs.recommended,
    ...pluginVue.configs['flat/recommended'],
    {
        languageOptions: {
            ecmaVersion: 'latest',
            sourceType: 'module',
            parser: vueParser,
            parserOptions: {
                parser: parserTs,
            },
        },
        plugins: {
            '@stylistic/ts': stylisticTs
        },
        rules: {
            'semi': ['error', 'always'],
            'vue/multi-word-component-names': 0,
            'no-console': 2,
            '@typescript-eslint/no-explicit-any': 0,
            'vue/html-indent': ['error', 4],

            "vue/first-attribute-linebreak": ["error", {
                "singleline": "ignore",
                "multiline": "below"
            }],
            'camelcase': ['error', {'properties': 'never'}],
            'vue/html-closing-bracket-spacing': 'off',
            '@stylistic/ts/quotes': [
                'error',
                'single',
                {
                    'avoidEscape': true,
                    'allowTemplateLiterals': true
                }
            ]
        }
    },
)
