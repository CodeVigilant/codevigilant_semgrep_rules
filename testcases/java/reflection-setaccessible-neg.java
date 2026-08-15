public class ReflectionNeg {
    public void touch(Class<?> c) throws Exception {
        java.lang.reflect.Field f = c.getDeclaredField("record");
        f.setAccessible(false); // access checks stay on
        java.lang.reflect.Method m = c.getDeclaredMethod("monitor", Object.class);
        // goes through the public API instead of reflection
        Object v = c.newInstance();
        m.setAccessible(false);
        Object r = m.invoke(v, new Object());
    }
}
