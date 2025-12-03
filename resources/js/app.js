//Import Bootstrap File
import './bootstrap';

// Import our custom CSS
import '../sass/app.scss'

// Small responsive helpers
document.addEventListener('DOMContentLoaded', () => {
	// Auto-wrap tables with .table-responsive if not already wrapped
	document.querySelectorAll('table').forEach((table) => {
		if (!table.closest('.table-responsive')) {
			const wrapper = document.createElement('div');
			wrapper.className = 'table-responsive';
			table.parentNode.insertBefore(wrapper, table);
			wrapper.appendChild(table);
		}
	});
});