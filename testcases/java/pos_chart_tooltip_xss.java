// POSITIVE (should fire): mirrors the real Kotlin tooltip-generator shape precisely,
// with the dataset-derived build name into format() unescaped.
package com.dg.watcher;
import org.jfree.chart.labels.CategoryToolTipGenerator;
import org.jfree.data.category.CategoryDataset;
import java.util.Locale;

public class PosTooltipGen implements CategoryToolTipGenerator {
    private static final String GRAPH_TOOLTIP = "Build %s: %.1f Megabytes";

    @Override
    public String generateToolTip(CategoryDataset categoryDataset, int series, int itemIndex) {
        String buildName = categoryDataset.getColumnKey(itemIndex).toString();
        return String.format(Locale.ENGLISH, GRAPH_TOOLTIP, buildName, categoryDataset.getValue(series, itemIndex));
    }
}