#include <GL/glut.h>
#include <stdlib.h>
#include <math.h>

#if !defined(GLUT_WHEEL_UP)  
# define GLUT_WHEEL_UP 3  
# define GLUT_WHEEL_DOWN 4  
#endif 

/*  Set initial size of the display window.  */
GLsizei winWidth = 600, winHeight = 600;  

/*  Set size of world-coordinate clipping window.  */
GLfloat xwcMin = -50.0, xwcMax = 50.0;
GLfloat ywcMin = -50.0, ywcMax = 50.0;

bool dragging = false;
GLfloat oldX = 0, oldY = 0;
GLfloat translateX = 0, translateY = 0;
GLfloat zoom = 1.0;

class wcPt3D {
   public:
      GLfloat x, y, z;
};

void init (void)
{
   /*  Set color of display window to white.  */
   glClearColor (1.0, 1.0, 1.0, 0.0);
}

void plotPoint (wcPt3D bezCurvePt)
{
    glBegin (GL_POINTS);
    glVertex2f (bezCurvePt.x, bezCurvePt.y);
    glEnd ( );
}

/*  Compute binomial coefficients C for given value of n.  */
void binomialCoeffs (GLint n, GLint * C)
{
   GLint k, j;

   for (k = 0;  k <= n;  k++) {
      /*  Compute n!/(k!(n - k)!).  */
      C [k] = 1;
      for (j = n;  j >= k + 1;  j--)
        C [k] *= j;
      for (j = n - k;  j >= 2;  j--)
        C [k] /= j;
   }
}

void computeBezPt (GLfloat t, wcPt3D * bezPt, GLint nCtrlPts,
                    wcPt3D * ctrlPts, GLint * C)
{
   GLint k, n = nCtrlPts - 1;
   GLfloat bezBlendFcn;

   bezPt->x = bezPt->y = bezPt->z = 0.0;

   /*  Compute blending functions and blend control points. */
   for (k = 0; k < nCtrlPts; k++) {
      bezBlendFcn = C [k] * pow (t, k) * pow (1 - t, n - k);
      bezPt->x += ctrlPts [k].x * bezBlendFcn;
      bezPt->y += ctrlPts [k].y * bezBlendFcn;
      bezPt->z += ctrlPts [k].z * bezBlendFcn;
   }
}

void bezier (wcPt3D * ctrlPts, GLint nCtrlPts, GLint nBezCurvePts)
{
   wcPt3D bezCurvePt;
   GLfloat t;
   GLint *C;

   /*  Allocate space for binomial coefficients  */
   C = new GLint [nCtrlPts];

   binomialCoeffs (nCtrlPts - 1, C);
   for (int i = 0;  i <= nBezCurvePts;  i++) {
      t = GLfloat (i) / GLfloat (nBezCurvePts);
      computeBezPt (t, &bezCurvePt, nCtrlPts, ctrlPts, C);
      plotPoint (bezCurvePt);
   }
   delete [ ] C;
}

void displayFcn (void)
{
   /*  Set example number of control points and number of
    *  curve positions to be plotted along the Bezier curve.
    */
   


   glClear (GL_COLOR_BUFFER_BIT);   //  Clear display window.
   glMatrixMode(GL_MODELVIEW); 
   glLoadIdentity();
   glScalef(zoom, zoom, 1.0);
   glTranslatef(translateX, translateY, 0.0);
   glPointSize (4);
   glColor3f (1.0, 0.0, 0.0);   //  Set point color to red.
   GLint nCtrlPts = 4, nBezCurvePts = 1000;

   wcPt3D ctrlPts [4] = { {-40.0, -40.0, 0.0}, {-10.0, 200.0, 0.0},
                 {10.0, -200.0, 0.0}, {40.0, 40.0, 0.0} };

   bezier (ctrlPts, nCtrlPts, nBezCurvePts);
   
   glutSwapBuffers();
}

void winReshapeFcn (GLint newWidth, GLint newHeight)
{
   /*  Maintain an aspect ratio of 1.0.  */
   glViewport (0, 0, newHeight, newHeight);

   glMatrixMode (GL_PROJECTION);
   glLoadIdentity ( );

   gluOrtho2D (xwcMin, xwcMax, ywcMin, ywcMax);
   winWidth = newWidth;
   winHeight = newHeight;

   glutPostRedisplay();
}

void mouseFunc(int button, int state, int x, int y) {
    if (button == GLUT_LEFT_BUTTON) {
        if (state == GLUT_DOWN) {
            
            dragging = true;
            oldX = x;
            oldY = y;
        } else if (state == GLUT_UP) {
            
            dragging = false;
        }
    }
    if ((button == GLUT_WHEEL_UP || button == GLUT_WHEEL_DOWN) && glutGetModifiers() == GLUT_ACTIVE_CTRL) {
        GLfloat zoomFactor = (button == GLUT_WHEEL_UP) ? 1.1 : 0.9;
        zoom *= zoomFactor;
        glutPostRedisplay();
    }
}

void mouseMotionFunc(int x, int y) {
    if (dragging) {
        
        GLfloat dx = (x - oldX) * (xwcMax - xwcMin) / winWidth;
        GLfloat dy = -(y - oldY) * (ywcMax - ywcMin) / winHeight;

        
        translateX += dx;
        translateY += dy;

        
        oldX = x;
        oldY = y;

       
        glutPostRedisplay();
    }
}



void keyboardFunc(unsigned char key, int x, int y) {
    switch (key) {
        case 'z': // Zoom out
            zoom /= 1.1;
            glutPostRedisplay();
            break;
        case 'Z': // Zoom in
            zoom *= 1.1;
            glutPostRedisplay();
            break;
        default:
            break;
    }
}


int main (int argc, char** argv)
{
   glutInit (&argc, argv);
   glutInitDisplayMode (GLUT_DOUBLE | GLUT_RGB);
   glutInitWindowPosition (50, 50);
   glutInitWindowSize (winWidth, winHeight);
   glutCreateWindow ("Bezier Curve");

   init ( );
   glutDisplayFunc (displayFcn);
   glutReshapeFunc (winReshapeFcn);
   glutMouseFunc(mouseFunc);
   glutMotionFunc(mouseMotionFunc);
   glutKeyboardFunc(keyboardFunc); 
   glutMainLoop ( );
}
