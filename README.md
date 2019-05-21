# ee-dandycat
Expression Engine 5: Entry look-ups with mixed category filters are just fine and dandy

It has been said that ExpressionEngine categories support both AND -and-
OR, but not both. This module changes that. Now you can choose those
complicated configurations for your picture entry where you want to show
"(pencil drawing OR photo) BUT NOT at the beach" or your physical
location lookup that should match "store BUT NOT (sidwalk shop OR
website)".

# Usage
This should work as a drop-in replacement for {exp:channel:entries}. There is a slightly higher penalty for calling Dandy Cat, so use {exp:channel:entries} unless you need the extra power.

In the category parameter, you can use parenthesis to group category
IDs.

```html
{exp:dandy_cat:entries category="(2|5)&((!3|7)|(4|1))"}
    <p>{entry_id} - {title}</p>
{/exp:dandy_cat:entries}
```
