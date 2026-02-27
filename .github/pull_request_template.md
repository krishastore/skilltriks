### Description

<!-- Provide a concise description of what this pull request addresses. -->

### Related Issues

<!-- Reference any related issues, to-do, e.g., "Closes #123" -->

### Checklist

**Code Quality**

- [ ] Code follows WordPress Coding Standards (PHPCS compliant).
- [ ] Clear naming for functions, hooks, classes, and variables.
- [ ] No unused/debug code and no deprecated functions.
- [ ] Documentation added for non-trivial logic.

**Performance**

- [ ] No unnecessary queries or heavy operations in loops.
- [ ] Queries and hooks are optimized.
- [ ] No redundant or duplicate logic introduced.

**Security**

- [ ] All inputs are sanitized, validated, and properly escaped.
- [ ] Nonce and capability checks implemented where required.
- [ ] Safe database queries ($wpdb->prepare() if applicable).
- [ ] No hardcoded secrets or sensitive data.

**Reviewer Confirmation:**

- [ ] Code reviewed, tested, and ready to merge OR feedback provided.
