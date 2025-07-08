// tests/setup.js
import moduleAlias from 'module-alias';
import path from 'path';

const __dirname = path.dirname(new URL(import.meta.url).pathname);

// Adjust the alias to point to your src folder (update if it's in a different location)
moduleAlias.addAliases({
  '@': path.resolve(__dirname, '../../src'),  // This will point to the 'src' folder from 'tests/setup.js'
});
