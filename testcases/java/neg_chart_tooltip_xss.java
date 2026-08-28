// NEGATIVE (should NOT fire): dataset-derived value HTML-escaped before format()
package com.example;
import java.text.NumberFormat;
import java.util.Locale;
import org.apache.commons.lang.StringEscapeUtils;
import org.jfree.chart.labels.CategoryToolTipGenerator;
import org.jfree.data.category.CategoryDataset;

class NegTooltipGen implements CategoryToolTipGenerator {
    private static final String TMPL = "Build %s: %.1f MB";
    @Override
    public String generateToolTip(CategoryDataset ds, int series, int item) {
        String rowName = StringEscapeUtils.escapeHtml(ds.getColumnKey(item).toString());
        return String.format(Locale.ENGLISH, TMPL, rowName, ds.getValue(series, item).floatValue());
    }
}