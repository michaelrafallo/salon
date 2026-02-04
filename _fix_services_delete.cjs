const fs = require('fs');
const path = 'resources/views/salon/services/index.blade.php';
let s = fs.readFileSync(path, 'utf8');
const old = /function deleteService\(id, name\) \{\s*if \(!confirm\([^)]+\)\) return;\s*salonApi\.delete\(apiServicesUrl \+ '\/' \+ id\)\.then\(function\(\) \{\s*showSuccessMessage\('Service deleted\.'\);\s*allServices = allServices\.filter\(function\(s\) \{ return s\.id !== id && s\.id !== parseInt\(id, 10\); \}\);\s*applyFilters\(\);\s*renderServices\(\);\s*\}\)\.catch\(function\(err\) \{\s*showErrorMessage\(err\.message \|\| 'Failed to delete service\.'\);\s*\}\);\s*\};/;
const replacement = `function deleteService(id, name) {
    openConfirmModal({
        title: 'Delete service',
        message: 'You are about to permanently remove this service' + (name ? ': ' + (name || '').replace(/"/g, '\\\\"') + '' : '') + '. Do you want to continue?',
        confirmLabel: 'Delete',
        onConfirm: function() {
            salonApi.delete(apiServicesUrl + '/' + id).then(function() {
                showSuccessMessage('Service deleted.');
                allServices = allServices.filter(function(s) { return s.id !== id && s.id !== parseInt(id, 10); });
                applyFilters();
                renderServices();
            }).catch(function(err) {
                showErrorMessage(err.message || 'Failed to delete service.');
            });
        }
    });
};`;
if (old.test(s)) {
  s = s.replace(old, replacement);
  fs.writeFileSync(path, s);
  console.log('Replaced successfully');
} else {
  console.log('Pattern not found');
}
