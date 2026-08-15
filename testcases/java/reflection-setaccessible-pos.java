public class ReflectionPos {
    public void touch(Class<?> c) throws Exception {
        java.lang.reflect.Field f = c.getDeclaredField("record");
        f.setAccessible(true);
        Object v = f.get(c.newInstance());
        java.lang.reflect.Method m = c.getDeclaredMethod("monitor", Object.class);
        m.accessible = true; // groovy-style property assignment
        Object r = m.invoke(v, new Object());
    }
}
