/*
 * CS4815 - Computer Graphics
 *
 * spin.cc - skeleton file for week 04 assignment.
 */


#include <math.h>
#include <stdlib.h>

#include <time.h>
#include <sys/time.h>

#include <GL/gl.h>
#include <GL/glu.h>
#include <GL/glut.h>



#define WINDOW_WIDTH        512
#define WINDOW_HEIGHT       512
#define WINDOW_CAPTION      "CS4815: Week 04"

#define ROTATE_CLOCKWISE        1
#define ROTATE_COUNTERCLOCKWISE 2
#define INCREASE_SPEED          3
#define DECREASE_SPEED          4
#define COLOR_RED               5
#define COLOR_GREEN             6
#define COLOR_BLUE              7
#define REVERSE_DIRECTION       8
#define SPEED_DOUBLE            9
#define SPEED_HALF              10
#define QUIT                    11





/*
 * Globals
 */
int timer = 0;
float angle = 0;
int rotate_direction = 1; 
float rotation_speed = 0.2; 
float color[3] = {1.0, 1.0, 1.0};

void processMenuEvents(int option) {
    switch(option) {
      case ROTATE_CLOCKWISE:
    rotate_direction = 1;
    break;
case ROTATE_COUNTERCLOCKWISE:
    rotate_direction = -1;
    break;

case INCREASE_SPEED:
    rotation_speed += 0.1;
    break;
case DECREASE_SPEED:
    rotation_speed -= 0.1; 
    break;

case COLOR_RED:
    color[0] = 1.0; color[1] = 0.0; color[2] = 0.0;
    break;
case COLOR_GREEN:
    color[0] = 0.0; color[1] = 1.0; color[2] = 0.0;
    break;
case COLOR_BLUE:
    color[0] = 0.0; color[1] = 0.0; color[2] = 1.0;
    break;

        
        case REVERSE_DIRECTION:
            rotate_direction *= -1; 
            break;
        case SPEED_DOUBLE:
            rotation_speed *= 2; 
            break;
        case SPEED_HALF:
            rotation_speed *= 0.5; 
            break;
        case QUIT:
            exit(0); 
            break;
    }
    glutPostRedisplay();
}

void createMenu() {
    
    glutCreateMenu(processMenuEvents);
    
    
    int directionMenu = glutCreateMenu(processMenuEvents);
    glutAddMenuEntry("Clockwise", ROTATE_CLOCKWISE);
    glutAddMenuEntry("Counter-Clockwise", ROTATE_COUNTERCLOCKWISE);
    glutAddMenuEntry("Reverse", REVERSE_DIRECTION);

    
    int speedMenu = glutCreateMenu(processMenuEvents);
    glutAddMenuEntry("Increase", INCREASE_SPEED);
    glutAddMenuEntry("Decrease", DECREASE_SPEED);
    glutAddMenuEntry("Double", SPEED_DOUBLE);
    glutAddMenuEntry("Half", SPEED_HALF);

    
    int colorMenu = glutCreateMenu(processMenuEvents);
    glutAddMenuEntry("Red", COLOR_RED);
    glutAddMenuEntry("Green", COLOR_GREEN);
    glutAddMenuEntry("Blue", COLOR_BLUE);

    
    glutCreateMenu(processMenuEvents);
    glutAddSubMenu("Direction", directionMenu);
    glutAddSubMenu("Speed", speedMenu);
    glutAddSubMenu("Color", colorMenu);
    glutAddMenuEntry("Quit", QUIT);

   
    glutAttachMenu(GLUT_RIGHT_BUTTON);
}




void idle(void) {
    angle += rotation_speed * rotate_direction;
    if (angle >= 360.0) {
        angle -= 360.0;
    } else if (angle <= -360.0) {
        angle += 360.0;
    }
    glutPostRedisplay();
}

/**
 * Return number of timer ticks (miliseconds).
 */
int
get_ticks()
{
    struct timeval tv;
    if (gettimeofday(&tv, NULL))
        return 0;

    return tv.tv_sec * 1000 + (tv.tv_usec / 1000);
}

    
/**
 * Initialise OpenGL state variables.
 */
void init_gl()
{
    /* Set the background color to be light blue.  */
    glClearColor(0.75f, 0.75f, 1.0f, 1.0f);
    createMenu();
}


/**
 * Re-display callback funcion.
 *
 * This function is called when contents of the window need to be
 * repainted.
 */
void display(void) {
    glClear(GL_COLOR_BUFFER_BIT);
    glLoadIdentity();

  
    if (rotate_direction != 0) {
        angle += rotation_speed * rotate_direction;
        if (angle >= 360.0) angle -= 360.0;
        else if (angle <= -360.0) angle += 360.0;
    }

    glRotatef(angle, 0.0f, 0.0f, 1.0f);
    glColor3fv(color);

    glBegin(GL_QUADS);
        glVertex2f(-0.5f, -0.5f);
        glVertex2f(0.5f, -0.5f);
        glVertex2f(0.5f, 0.5f);
        glVertex2f(-0.5f, 0.5f);
    glEnd();

    glutSwapBuffers();
}


/**
 * Window resize callback function.
 *
 * This function is called when application window is resized.
 */
void
reshape(int width, int height)
{
    /* Update the viewport area to occupy the entire window.  */
    glViewport(0, 0, width, height);


    /* Refresh the screen contents.  */
    glutPostRedisplay();
}


/**
 * Keyboard callback function.
 *
 * This function is called when a key is pressed/released.
 */
void
keyboard(unsigned char key, int x, int y)
{
}


/**
 * Special key callback function.
 *
 * This function is called when a special key is pressed/released.
 * Special keys include: SHIFT, ALT ...
 */
void
special(int key, int x, int y)
{
}


/**
 * Mouse button callback function.
 *
 * This function is called when a mouse button is pressed/releassed.
 */
void
mouse(int button, int state, int x, int y)
{
    /* Left button starts rotatation.  */
    if (button == GLUT_LEFT_BUTTON && state == GLUT_DOWN) {
        timer = get_ticks();
        rotate_direction = 1;
    }
    /* Middle button stops rotation.  */
  else if (button == GLUT_MIDDLE_BUTTON && state == GLUT_DOWN) {
        rotate_direction = 0; // Stop rotation
    }

}


/**
 * Mouse motion callback function. 
 *
 * This function is called when mouse is moved (passive) and one of the
 * buttons is pressed (normal).
 */
void
motion(int x, int y)
{
}


/**
 * Idle callback function.
 *
 * This function is called when the program is idle (nothing to do).
 */



/** 
 * `main' function is every C program entry point.
 *
 * This is where the execution starts.
 */
int
main(int argc, char *argv[])
{
    /* Initialise the GLUT library.  */
    glutInit(&argc, argv);

    /* Specify bufffer format, double buffered RGBA.  */
    glutInitDisplayMode(GLUT_DOUBLE | GLUT_RGB);

    /* Create an OpenGL capable window. This also initialises OpenGL
     * context.
     *
     * NOTE: You are not allowed to call any OpenGL function prior to
     * the window creation!
     */
    glutInitWindowPosition(0,0);
    glutInitWindowSize(WINDOW_WIDTH, WINDOW_HEIGHT);
    glutCreateWindow(WINDOW_CAPTION);


    /* Initialise application state.  */
    init_gl();

    /* Register callback functions. Callback functions are called by
     * GLUT in response to various events.
     */
    glutDisplayFunc(display);
    glutReshapeFunc(reshape);
    glutKeyboardFunc(keyboard);
    /* glutSpecialFunc(special); */
    glutMouseFunc(mouse);
    glutMotionFunc(motion);
    glutIdleFunc(idle);
    createMenu();
    /* glutPassiveMotionFunc(motion); */
    /* glutIdleFunc(idle); */

    /* Enter the event processing loop. All the mouse, keyboard, screen
     * events will be processed and dispatchted to earlier registered
     * callback functions.
     */
    glutMainLoop();
    return EXIT_SUCCESS;
}

/* vi:set tw=72 sw=4 ts=4 et: */
